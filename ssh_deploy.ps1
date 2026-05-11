$ip = "173.249.52.218"
$user = "root"
$pass = "@Amjad2025-#O"

# Try to run a command via SSH and send password
$cmd = "ls /home/kashly/public_html"
$sshCmd = "ssh -o StrictHostKeyChecking=no $user@$ip '$cmd'"

$wshell = New-Object -ComObject WScript.Shell
$proc = $wshell.Exec($sshCmd)
Start-Sleep -Milliseconds 500
$wshell.AppActivate($proc.ProcessID)
$wshell.SendKeys($pass + "{ENTER}")
Start-Sleep -Seconds 2
while (!$proc.StdOut.AtEndOfStream) {
    $proc.StdOut.ReadLine()
}
