 
    self.onmessage = function (event) {
        const data       = event.data;
        // Perform a heavy computation or task
        id               = data[0];
        business_id      = data[1];
        result           = heavyComputation(id,business_id);
        // Post the result back to the main thread
        self.postMessage(result);
    };
    function heavyComputation(id,business_id) {
        // Example of a heavy computation task 
        url = '/purchases/get-balance?id='+id+'&business_id='+business_id;
        fetch(url) 
            .then(data => {
                // Post the result back to the main thread 
                self.postMessage("true") ;
            })
            .catch(error => {
                // Post the error back to the main thread
                self.postMessage("false") ;
                self.postMessage({ error: error.message });
            });
    }
    