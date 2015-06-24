/**
 * Console history
 */
self.addEventListener('message', function (e) {

    var i = 0;

    setInterval(function() {

        var request = new XMLHttpRequest();


        request.onreadystatechange  = function() {
            if (request.readyState == 4 && request.status == 200){
                postMessage({"data": request.responseText });
            }
        };

        request.onerror = function() {
            // There was a connection error of some sort
            postMessage({"error": "Oopps!"});
        };

        request.open('POST', './../../../index.php', true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        request.send("h=1");

    }, 1500);

}, false);