; Inno Setup Script for Sales Application
; This will generate the .EXE installer you requested.
; Install Inno Setup (free) and compile this file.

[Setup]
AppName=Sales Management System
AppVersion=1.0
DefaultDirName=C:\xampp\htdocs\Sales
DefaultGroupName=Sales Management System
OutputBaseFilename=Sales_System_Setup
Compression=lzma
SolidCompression=yes
PrivilegesRequired=admin

[Files]
Source: "C:\xampp\htdocs\Sales\*"; DestDir: "{app}"; Flags: recursesubdirs createallsubdirs
Source: "C:\xampp\htdocs\Sales\prerequisites\*"; DestDir: "{tmp}"; Flags: deleteafterinstall

[Icons]
Name: "{group}\Sales Application"; Filename: "http://localhost/Sales/public"
Name: "{commondesktop}\Sales Application"; Filename: "http://localhost/Sales/public"

[Run]
; Install Node.js if missing (Silent Mode)
Filename: "{tmp}\node-installer.msi"; Parameters: "/quiet /qn /norestart"; StatusMsg: "Installing Node.js Environment..."; Check: NotNodeInstalled
; Install XAMPP (Unattended Mode - assumes xampp-installer.exe exists)
Filename: "{tmp}\xampp-installer.exe"; Parameters: "--mode unattended"; StatusMsg: "Installing XAMPP (MySQL & PHP)..."; Check: NotXAMPPInstalled
; Final App Setup
Filename: "{app}\setup_application.bat"; Description: "Completing environment setup..."; Flags: postinstall waituntilterminated

[Code]
function NotNodeInstalled: Boolean;
begin
  Result := Not FileExists('C:\Program Files\nodejs\node.exe');
end;

function NotXAMPPInstalled: Boolean;
begin
  Result := Not DirExists('C:\xampp');
end;
