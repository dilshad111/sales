$WshShell = New-Object -ComObject WScript.Shell
$ShortcutPath = "$([Environment]::GetFolderPath('Desktop'))\Sales Application.lnk"
$AppPath = "C:\xampp\htdocs\Sales\public"
$BrowserPath = "C:\Program Files\Google\Chrome\Application\chrome.exe"

# If Chrome is not found, use msedge
if (-not (Test-Path $BrowserPath)) {
    $BrowserPath = "C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe"
}

$Shortcut = $WshShell.CreateShortcut($ShortcutPath)
$Shortcut.TargetPath = $BrowserPath
$Shortcut.Arguments = "http://localhost/Sales/public"
$Shortcut.IconLocation = "C:\xampp\htdocs\Sales\public\favicon.ico"
$Shortcut.Description = "Open Sales Management System"
$Shortcut.Save()

Write-Host "Desktop Shortcut Created Successfully!" -ForegroundColor Green
