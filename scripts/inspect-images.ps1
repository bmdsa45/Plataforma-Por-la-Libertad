Add-Type -AssemblyName System.Drawing
$files = @("E:\PPLuy\ppluy.jpg","E:\PPLuy\ppl.jpg")
foreach ($f in $files) {
  if (Test-Path $f) {
    $img = [System.Drawing.Image]::FromFile($f)
    "{0} => {1}x{2}, {3} bytes" -f (Split-Path -Leaf $f), $img.Width, $img.Height, (Get-Item $f).Length
    $img.Dispose()
  } else {
    "{0} => NOT FOUND" -f (Split-Path -Leaf $f)
  }
}