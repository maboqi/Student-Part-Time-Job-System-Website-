function loadContent(page) {
    let content = "";

    if (page === 'profile') {
        content = "profile.html";  
    } else if (page === 'job') {
        content = "job.html";  // load job.html content, but still need fetch
    } else if (page === 'application') {
        content = "application.html";  
    }

    //fetch function to load HTML files
    if (content.endsWith(".html")) {
        fetch(content)
            .then(response => response.text())  // retreive html content
            .then(html => {
                document.getElementById("content").innerHTML = html;
            })
            .catch(error => {
                console.error("Error loading HTML file:", error);
            });

    } else {

        document.getElementById("content").innerHTML = content;
    }
}
