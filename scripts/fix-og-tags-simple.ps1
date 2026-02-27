$targetUrl = 'http://localhost:8080/img/og/ppl-1200x630.jpg'
$ogMeta = '<meta property="og:image" content="' + $targetUrl + '">' 
$twMeta = '<meta name="twitter:image" content="' + $targetUrl + '">' 

$files = Get-ChildItem -Path 'E:\PPLuy' -Filter *.html -File
foreach ($file in $files) {
  $text = Get-Content -Raw -Path $file.FullName
  $orig = $text

  # Replace bare URL occurrences with OG meta tag
  $text = $text.Replace($targetUrl, $ogMeta)

  # If a OG meta tag appears after twitter:description, convert it to twitter:image
  $twDescIdx = $text.IndexOf('twitter:description')
  if ($twDescIdx -ge 0) {
    $ogIdx = $text.IndexOf('<meta property="og:image"', $twDescIdx)
    if ($ogIdx -ge 0) {
      $endIdx = $text.IndexOf('>', $ogIdx)
      if ($endIdx -ge 0) {
        $text = $text.Substring(0, $ogIdx) + $twMeta + $text.Substring($endIdx + 1)
      }
    }
  }

  if ($text -ne $orig) {
    Set-Content -Path $file.FullName -Value $text -Encoding UTF8
    Write-Output ("Fixed OG/Twitter image tags: {0}" -f $file.Name)
  }
}