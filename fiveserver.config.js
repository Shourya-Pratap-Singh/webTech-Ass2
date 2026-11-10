// fiveserver.config.js
module.exports = {
  php: "C:\\xampp\\php\\php.exe",
  open: "index.html",
  highlight: true,
  injectBody: true,
  navigate: true,
  proxy: {
    "/process.php": "http://127.0.0.1:8000/process.php"
  }
};
