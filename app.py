from flask import Flask, request, render_template_string
import zipfile
import plistlib
import tempfile
import os

app = Flask(__name__)

HTML = """
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>IPA Analyzer</title>
<style>
body{
background:#111;
color:white;
font-family:Arial;
text-align:center;
padding:40px;
}
.container{
background:#222;
padding:30px;
border-radius:12px;
width:450px;
margin:auto;
box-shadow:0 0 15px rgba(0,0,0,0.6);
}
h1{
margin-bottom:20px;
}
input{
padding:10px;
margin:10px;
background:#333;
color:white;
border:none;
border-radius:6px;
}
button{
padding:10px 25px;
background:#00aaff;
border:none;
border-radius:6px;
color:white;
cursor:pointer;
}
button:hover{
background:#008ecc;
}
.info{
margin-top:20px;
text-align:left;
background:#111;
padding:15px;
border-radius:8px;
}
</style>
<script>
function showLoading(){
document.getElementById("status").innerHTML="Analyzing IPA file...";
}
</script>
</head>
<body>
<div class="container">
<h1>IPA File Analyzer</h1>
<form method="post" enctype="multipart/form-data" onsubmit="showLoading()">
<input type="file" name="ipa" accept=".ipa" required>
<br>
<button type="submit">Upload & Analyze</button>
</form>
<p id="status"></p>
{% if info %}
<div class="info">
<h3>Application Information</h3>
<p><b>Name:</b> {{info.name}}</p>
<p><b>Bundle ID:</b> {{info.bundle}}</p>
<p><b>Version:</b> {{info.version}}</p>
<p><b>Build:</b> {{info.build}}</p>
<p><b>Minimum iOS:</b> {{info.ios}}</p>
</div>
{% endif %}
</div>
</body>
</html>
"""

@app.route("/", methods=["GET","POST"])
def home():
    info = None
    if request.method == "POST":
        file = request.files.get("ipa")
        if file:
            temp = tempfile.NamedTemporaryFile(delete=False)
            file.save(temp.name)
            try:
                with zipfile.ZipFile(temp.name, "r") as ipa:
                    for name in ipa.namelist():
                        if "Info.plist" in name:
                            plist = plistlib.loads(ipa.read(name))
                            info = {
                                "name": plist.get("CFBundleName", "Unknown"),
                                "bundle": plist.get("CFBundleIdentifier", "Unknown"),
                                "version": plist.get("CFBundleShortVersionString", "Unknown"),
                                "build": plist.get("CFBundleVersion", "Unknown"),
                                "ios": plist.get("MinimumOSVersion", "Unknown")
                            }
                            break
            except Exception as e:
                info = {"name": f"Error reading IPA: {e}"}
            finally:
                os.remove(temp.name)
    return render_template_string(HTML, info=info)

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000)
