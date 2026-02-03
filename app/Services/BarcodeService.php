<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookCopy;
use Picqer\Barcode\BarcodeGeneratorSVG;

class BarcodeService
{
    protected BarcodeGeneratorSVG $generator;

    public function __construct()
    {
        $this->generator = new BarcodeGeneratorSVG();
    }

    /**
     * Generate barcodes for a book based on quantity
     * 
     * @param Book $book
     * @param int $quantity Number of barcodes to generate
     * @return array Array of generated BookCopy records
     */
    public function generateBarcodes(Book $book, int $quantity): array
    {
        $existingCopies = $book->copies()->count();
        $availableSlots = $book->stock - $existingCopies;

        if ($quantity > $availableSlots) {
            throw new \InvalidArgumentException(
                "Jumlah barcode yang diminta ({$quantity}) melebihi stok tersedia ({$availableSlots})"
            );
        }

        $generatedCopies = [];

        for ($i = 0; $i < $quantity; $i++) {
            $copyNumber = $existingCopies + $i + 1;
            $barcode = $this->generateBarcodeString($book->code, $copyNumber);

            $bookCopy = BookCopy::create([
                'book_id' => $book->id,
                'barcode' => $barcode,
                'status' => 'available',
            ]);

            $generatedCopies[] = $bookCopy;
        }

        return $generatedCopies;
    }

    /**
     * Generate barcode string in format: BOOKCODE-XXX
     * 
     * @param string $bookCode
     * @param int $copyNumber
     * @return string
     */
    public function generateBarcodeString(string $bookCode, int $copyNumber): string
    {
        return sprintf('%s-%03d', $bookCode, $copyNumber);
    }

    /**
     * Generate barcode image as base64 encoded SVG
     * 
     * @param string $barcodeString
     * @return string Base64 encoded SVG image
     */
    public function generateBarcodeImage(string $barcodeString): string
    {
        $barcode = $this->generator->getBarcode($barcodeString, $this->generator::TYPE_CODE_128);
        return base64_encode($barcode);
    }

    /**
     * Get barcode data for printing (includes image and metadata)
     * 
     * @param BookCopy $bookCopy
     * @return array
     */
    public function getBarcodeData(BookCopy $bookCopy): array
    {
        return [
            'barcode' => $bookCopy->barcode,
            'image' => $this->generateBarcodeImage($bookCopy->barcode),
            'title' => $bookCopy->book->title,
            'code' => $bookCopy->book->code,
        ];
    }

    /**
     * Get multiple barcode data for printing
     * 
     * @param array $bookCopyIds
     * @return array
     */
    public function getBarcodesForPrinting(array $bookCopyIds): array
    {
        $bookCopies = BookCopy::with('book')
            ->whereIn('id', $bookCopyIds)
            ->get();

        return $bookCopies->map(fn($copy) => $this->getBarcodeData($copy))->toArray();
    }

    /**
     * Calculate how many barcodes can still be generated for a book
     * 
     * @param Book $book
     * @return int
     */
    public function getAvailableSlots(Book $book): int
    {
        return max(0, $book->stock - $book->copies()->count());
    }
}
