$files = Get-ChildItem -Path 'E:\PPLuy' -Filter *.html -File
$replacements = @{
  'Ã¡'='á'; 'Ã©'='é'; 'Ã­'='í'; 'Ã³'='ó'; 'Ãº'='ú'; 'Ã±'='ñ';
  'Ã'='Á'; 'Ã‰'='É'; 'Ã'='Í'; 'Ã“'='Ó'; 'Ãš'='Ú'; 'Ã‘'='Ñ';
  'Â¿'='¿'; 'Â¡'='¡'; 'Âº'='º'; 'Âª'='ª'; 'â€”'='—'
}
$twitterImage = 'http://localhost:8080/img/og/ppl-1200x630.jpg'
foreach ($file in $files) {
  $text = Get-Content -Raw -Path $file.FullName
  $orig = $text
  foreach ($k in $replacements.Keys) {
    $text = $text -replace [Regex]::Escape($k), [System.Text.RegularExpressions.MatchEvaluator] { param($m) $replacements[$k] }
  }
  # Update twitter:image whether name or property is used
  $text = [Regex]::Replace($text, '(<meta[^>]+(?:name|property)="twitter:image"[^>]+content=")([^"
]+)("[^>]*>)', "$1$twitterImage$3", [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
  if ($text -ne $orig) {
    Set-Content -Path $file.FullName -Value $text -Encoding UTF8
    Write-Output "Fixed: $($file.Name)"
  }
}