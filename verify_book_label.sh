#!/bin/bash

# 1. Initial page
echo "Fetching initial page..."
curl -s -b cookies.txt -c cookies.txt -L http://localhost:8000/books-label > initial_page.html
if grep -q "login" initial_page.html; then
    echo "Need to login..."
    # Get CSRF token
    CSRF_TOKEN=$(curl -s -c cookies.txt http://localhost:8000/login | grep -oP '(?<=name="_token" value=")[^"]*')
    echo "CSRF Token: $CSRF_TOKEN"
    
    # Login
    curl -s -b cookies.txt -c cookies.txt -L \
        -d "_token=$CSRF_TOKEN" \
        -d "email=test@example.com" \
        -d "password=password" \
        http://localhost:8000/login > /dev/null
    
    # Try again
    curl -s -b cookies.txt -c cookies.txt -L http://localhost:8000/books-label > initial_page.html
fi

echo "Initial page fetched. Checking for book list..."
grep -oP 'wire:click="selectBook\(\K[^)]+' initial_page.html | head -n 1 > book_id.txt
BOOK_ID=$(cat book_id.txt)

if [ -z "$BOOK_ID" ]; then
    echo "No books found."
    exit 1
fi

echo "Selected Book ID: $BOOK_ID"

# Since it's Livewire, we can't easily simulate the click with curl without complex payload.
# But we can verify the code logic and the presence of elements.
echo "Verifying UI elements in initial page..."
grep -q "Pilih Buku" initial_page.html && echo "Found 'Pilih Buku' header"
grep -q "Cari kode atau judul buku..." initial_page.html && echo "Found search input"

# Check if we can find the generate button logic in the component
echo "Verifying component logic..."
grep -q "public function generateLabels" app/Livewire/Book/BookLabelGenerator.php && echo "generateLabels method exists"
grep -q "public \$showPrintView = false" app/Livewire/Book/BookLabelGenerator.php && echo "showPrintView property exists"
