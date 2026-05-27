$ErrorActionPreference = "Stop"

$keyPath = "$env:USERPROFILE\.ssh\id_rsa"

# Check if key exists
if (Test-Path ($keyPath + ".pub")) {
    Write-Host "SSH key already exists!"
    Write-Host "Public key:"
    Get-Content ($keyPath + ".pub")
    exit 0
}

# Create .ssh directory
$null = New-Item -ItemType Directory -Path "$env:USERPROFILE\.ssh" -Force

# Generate SSH key using Start-Process with piped input
$pinfo = New-Object System.Diagnostics.ProcessStartInfo
$pinfo.FileName = "ssh-keygen"
$pinfo.Arguments = "-t rsa -b 4096 -C `"ciputra.bahtiar@gmail.com`" -f `"$keyPath`" -N `"`""
$pinfo.RedirectStandardInput = $true
$pinfo.RedirectStandardOutput = $true
$pinfo.RedirectStandardError = $true
$pinfo.UseShellExecute = $false
$pinfo.CreateNoWindow = $true

$process = New-Object System.Diagnostics.Process
$process.StartInfo = $pinfo
$process.Start() | Out-Null
$process.StandardInput.WriteLine("")
$process.StandardInput.Close()
$process.WaitForExit()

$stdout = $process.StandardOutput.ReadToEnd()
$stderr = $process.StandardError.ReadToEnd()

if ($process.ExitCode -eq 0) {
    Write-Host "SSH key generated successfully!"
    Write-Host "`nPublic key:"
    Get-Content ($keyPath + ".pub")
} else {
    Write-Host "Error: $stderr"
}