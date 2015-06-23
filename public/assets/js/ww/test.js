/**
 * Webworker
 */

self.addEventListener('message', function (e) {

    var i = 0;

    setInterval(function() {

        var request = new XMLHttpRequest();
        request.onreadystatechange  = function() {
            if (request.readyState == 4 && request.status == 200){
                postMessage({"data": request.responseText});
            }
            // if (request.status >= 200 && request.status <= 400) {
            //     // Success!
            //     var data = request.responseText;
            //     postMessage({"json": data});

            // } 
            else {
                // We reached our target server, but it returned an error
            }
        };


        request.open('POST', './../index.php', true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        request.onerror = function() {
          // There was a connection error of some sort
          postMessage({"error": "oh!"});
        };

        request.send("h=1");

    }, 1500);

}, false);