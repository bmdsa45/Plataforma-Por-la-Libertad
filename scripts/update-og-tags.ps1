$target = "http://localhost:8080/img/og/ppl-1200x630.jpg"
$files = Get-ChildItem -Path 'E:\PPLuy' -Filter *.html -File
foreach ($file in $files) {
  $text = Get-Content -Raw -Path $file.FullName
  $new = $text
  $patternOg = '(<meta[^>]+property="og:image"[^>]+content=")([^"]*ppluy\.jpg)("[^>]*>)'
  $patternTw = '(<meta[^>]+name="twitter:image"[^>]+content=")([^"]*ppluy\.jpg)("[^>]*>)'
  $new = [Regex]::Replace($new, $patternOg, "$1$target$3", [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
  $new = [Regex]::Replace($new, $patternTw, "$1$target$3", [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
  if ($new -ne $text) {
    Set-Content -Path $file.FullName -Value $new -Encoding UTF8
    Write-Output "Updated: $($file.Name)"
  }
}