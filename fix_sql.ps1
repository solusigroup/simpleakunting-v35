$inputFile = "D:\PROJECT_HERD\SimpleAkuntingv3-5\simkopde_umkm_fixed.sql"
$content = [System.IO.File]::ReadAllText($inputFile)

# Remove all CONSTRAINT ... FOREIGN KEY lines
$pattern = ',\s*CONSTRAINT\s+`[^`]*`\s+FOREIGN KEY\s*\([^)]+\)\s*REFERENCES\s*`[^`]*`\s*\([^)]+\)(\s*ON DELETE CASCADE)?(\s*ON UPDATE CASCADE)?'
$content = [regex]::Replace($content, $pattern, '')

[System.IO.File]::WriteAllText($inputFile, $content)
Write-Host "Done! FK constraints removed."
