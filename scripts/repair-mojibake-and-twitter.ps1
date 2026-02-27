$files = Get-ChildItem -Path 'E:\PPLuy' -Filter *.html -File
$twitterImage = 'http://localhost:8080/img/og/ppl-1200x630.jpg'
foreach ($file in $files) {
  $text = Get-Content -Raw -Path $file.FullName
  $orig = $text
  # Detect mojibake by presence of char codes 195 (Ã), 194 (Â), 226 (â)
  $hasMojibake = $false
  foreach ($ch in $text.ToCharArray()) {
    $code = [int][char]$ch
    if ($code -eq 195 -or $code -eq 194 -or $code -eq 226) { $hasMojibake = $true; break }
  }
  if ($hasMojibake) {
    $bytes = [System.Text.Encoding]::GetEncoding(1252).GetBytes($text)
    $fixed = [System.Text.Encoding]::UTF8.GetString($bytes)
    if (![string]::IsNullOrEmpty($fixed)) { $text = $fixed }
  }
  # Normalize Twitter image tag (name or property)
  $pattern = '(<meta[^>]+(?:name|property)="twitter:image"[^>]+content=")([^"\n]+)("[^>]*>)'
  $text = [Regex]::Replace($text, $pattern, "$1$twitterImage$3", [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
  if ($text -ne $orig) {
    Set-Content -Path $file.FullName -Value $text -Encoding UTF8
    Write-Output ("Repaired: {0}" -f $file.Name)
  }
}