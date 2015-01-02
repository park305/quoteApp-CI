  var buttonText = [];
  var httpRequest;

  window.onload = function () {
  buttonText = ["Next Quote", "Bring Me Another!", "What Else You Got?"];
   document.getElementById("nextBtn").onclick = function() { makeRequest('/quoteApp-CI/index.php/ajaxQuote'); };
}
  function makeRequest(url) {
    if (window.XMLHttpRequest) { // Mozilla, Safari, ...
      httpRequest = new XMLHttpRequest();
    } else if (window.ActiveXObject) { // IE
      try {
        httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
      } 
      catch (e) {
        try {
          httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
        } 
        catch (e) {}
      }
    }

    if (!httpRequest) {
      alert('Giving up :( Cannot create an XMLHTTP instance');
      return false;
    }
    httpRequest.onreadystatechange = alertContents;
    httpRequest.open('GET', url);
    httpRequest.send();
  }



  function alertContents() {
    if (httpRequest.readyState === 4) {
      if (httpRequest.status === 200) {
        var randnum = Math.floor((Math.random() * 2) + 1); 
        document.getElementById("nextBtn").innerHTML = buttonText[randnum];
        document.getElementById("quoteBody").innerHTML = httpRequest.responseText;
      } else {
        alert('There was a problem with the request.');
      }
    }
  }