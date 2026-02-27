$targetUrl = 'http://localhost:8080/img/og/ppl-1200x630.jpg'
$files = Get-ChildItem -Path 'E:\PPLuy' -Filter *.html -File
foreach ($file in $files) {
  $text = Get-Content -Raw -Path $file.FullName
  $orig = $text

  # Remove stray bare URL lines accidentally inserted (ASCII-only)
  $text = [Regex]::Replace($text, "(?m)^[ \t]*http://localhost:8080/img/og/ppl-1200x630\.jpg[ \t]*\r?\n", '')

  # Remove any existing og:image and twitter:image meta tags to reinsert cleanly
  $text = [Regex]::Replace($text, "(?mi)^\s*<meta[^>]+property=\"og:image\"[^>]*>\s*\r?\n", '')
  $text = [Regex]::Replace($text, "(?mi)^\s*<meta[^>]+(?:name|property)=\"twitter:image\"[^>]*>\s*\r?\n", '')

  # Prepare insertion strings
  $insertOg = "`r`n    <meta property=\"og:image\" content=\"$targetUrl\">"
  $insertTw = "`r`n    <meta name=\"twitter:image\" content=\"$targetUrl\">"

  # Insert og:image after og:description if present, else after og:url, else before </head>
  if ($text -match "(<meta[^>]+property=\"og:description\"[^>]*>)") {
    $text = [Regex]::Replace($text, "(<meta[^>]+property=\"og:description\"[^>]*>)", '$1' + $insertOg, [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
  } elseif ($text -match "(<meta[^>]+property=\"og:url\"[^>]*>)") {
    $text = [Regex]::Replace($text, "(<meta[^>]+property=\"og:url\"[^>]*>)", '$1' + $insertOg, [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
  } else {
    $text = [Regex]::Replace($text, "(</head>)", "    <meta property=\"og:image\" content=\"$targetUrl\">`r`n$1", [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
  }

  # Insert twitter:image after twitter:description if present, else after twitter:card, else before </head>
  if ($text -match "(<meta[^>]+(?:name|property)=\"twitter:description\"[^>]*>)") {
    $text = [Regex]::Replace($text, "(<meta[^>]+(?:name|property)=\"twitter:description\"[^>]*>)", '$1' + $insertTw, [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
  } elseif ($text -match "(<meta[^>]+(?:name|property)=\"twitter:card\"[^>]*>)") {
    $text = [Regex]::Replace($text, "(<meta[^>]+(?:name|property)=\"twitter:card\"[^>]*>)", '$1' + $insertTw, [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
  } else {
    $text = [Regex]::Replace($text, "(</head>)", "    <meta name=\"twitter:image\" content=\"$targetUrl\">`r`n$1", [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
  }

  if ($text -ne $orig) {
    Set-Content -Path $file.FullName -Value $text -Encoding UTF8
    Write-Output ("Fixed tags: {0}" -f $file.Name)
  }
}