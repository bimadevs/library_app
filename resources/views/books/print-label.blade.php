<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Label Buku</title>
    <style>
        @page {
            margin: 1cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
        }
        .label-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        .label-row {
            display: table-row;
        }
        .label-cell {
            display: table-cell;
            width: 6.4cm; /* Standard width for 3-column A4 labels */
            height: 3.4cm; /* Standard height */
            padding: 5px;
            vertical-align: top;
            /* border: 1px dashed #ccc; /* Uncomment for guide lines */
        }
        .label-content {
            border: 1px solid #000;
            height: 2.8cm;
            width: 5.8cm;
            margin: 0 auto;
            padding: 2px;
            text-align: center;
            display: block;
        }
        .library-name {
            font-size: 8pt;
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .call-number {
            font-weight: bold;
            font-size: 12pt;
            line-height: 1.2;
            margin-top: 5px;
        }
        .call-number span {
            display: block;
        }
        .classification {
            font-size: 12pt;
        }
        .author-code {
            font-size: 11pt;
            text-transform: uppercase;
        }
        .title-code {
            font-size: 11pt;
            text-transform: lowercase;
        }
        
        /* Force page break after every 21 labels (3x7 grid) */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    @php
        $colCount = 0;
        $totalCount = 0;
    @endphp

    <div class="label-grid">
        <div class="label-row">
            @foreach($books as $book)
                <div class="label-cell">
                    <div class="label-content">
                        <div class="library-name">PERPUSTAKAAN SMK MUDITA SINGKAWANG</div>
                        <div class="call-number">
                            <span class="classification">{{ $book->classification->ddc_code ?? '000' }}</span>
                            <span class="author-code">{{ substr($book->author, 0, 3) }}</span>
                            <span class="title-code">{{ substr($book->title, 0, 1) }}</span>
                        </div>
                    </div>
                </div>

                @php
                    $colCount++;
                    $totalCount++;
                @endphp

                @if($colCount % 3 == 0)
                    </div><div class="label-row">
                @endif

                @if($totalCount % 21 == 0 && !$loop->last)
                    </div></div><div class="page-break"></div><div class="label-grid"><div class="label-row">
                @endif
            @endforeach
            
            {{-- Fill empty cells to complete the row --}}
            @while($colCount % 3 != 0)
                <div class="label-cell"></div>
                @php $colCount++; @endphp
            @endwhile
        </div>
    </div>
</body>
</html>
