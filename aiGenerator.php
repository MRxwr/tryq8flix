<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <title>AI Art Generator</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            color: #fff;
            background: linear-gradient(135deg, #6b8cce, #9cc5f2, #6b8cce);
            background-size: 300% 300%;
            animation: moveBackground 15s infinite;
        }

        @keyframes moveBackground {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        h1, h2 {
            text-align: center;
            margin-top: 20px;
            text-shadow: 2px 2px #000;
        }

        .container-fluid {
            background: rgba(0, 0, 0, 0.7);
            border-radius: 10px;
            padding: 20px;
            margin: 30px auto;
            max-width: 800px;
        }

        footer {
            text-align: center;
            margin-top: 20px;
            color: white;
        }

        footer a {
            margin: 0 10px;
            color: white;
            text-decoration: none;
            font-size: 1.2rem;
        }

        footer a:hover {
            text-decoration: underline;
        }

        #loading {
            display: none;
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            text-align: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1100;
        }

        #loading .spinner {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
  </head>
  <body>
    <h1>AI Art Generator</h1>
    <h2>Transform Your Imagination Into Stunning Art</h2>
    <div class="container-fluid">
        <h3 class="text-center">Enter Your Prompt</h3>
        <div class="row text-center">
            <div class="col-12">
                <input name="prompt" id="prompt" value="" class="form-control" placeholder="Describe your art (e.g., a futuristic city)">
            </div>
            <div class="col-12 pt-3 text-center">
                <button id="submitBtn" class="btn btn-primary w-100">Generate Image</button>
            </div>
            <div class="col-12"><hr></div>
            <div class="col-12 pt-3" id="returnImage">
                <img style="width: 100%; border-radius: 10px;" id="frame" src="">
            </div>
            <div class="col-12 pt-3">
                <button id="downloadBtn" class="btn btn-success w-100 mb-2" disabled>Download</button>
                <button id="shareBtn" class="btn btn-warning w-100 mb-2" disabled>Share</button>
                <button id="savePromptBtn" class="btn btn-info w-100 mb-2" disabled>Save Prompt</button>
                <button id="viewHistoryBtn" class="btn btn-secondary w-100 mt-2">View Prompt History</button>
                <button id="clearCookiesBtn" class="btn btn-danger w-100 mt-2">Clear Cookies</button>
                <div id="historyList"></div>
            </div>
        </div>
    </div>
    <footer>
        <p>Share this website:</p>
        <a href="https://www.facebook.com/sharer/sharer.php?u=https://tryq8flix.com/aiGenerator.php" target="_blank">Facebook</a>
        <a href="https://twitter.com/intent/tweet?url=https://tryq8flix.com/aiGenerator.php" target="_blank">Twitter</a>
        <a href="https://wa.me/?text=Check out this amazing AI Art Generator: https://tryq8flix.com/aiGenerator.php" target="_blank">WhatsApp</a>
        <p>Courtesy of <a href="https://tryq8flix.com" target="_blank">TRYQ8FLiX</a></p>
    </footer>
    <div id="loading">
        <div class="spinner"></div>
        <p>Generating Your Art... Please Wait</p>
    </div>
    <div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100;"></div>
    <script>
        const promptInput = document.getElementById("prompt");
        const submitBtn = document.getElementById("submitBtn");
        const downloadBtn = document.getElementById("downloadBtn");
        const savePromptBtn = document.getElementById("savePromptBtn");
        const shareBtn = document.getElementById("shareBtn");
        const viewHistoryBtn = document.getElementById("viewHistoryBtn");
        const clearCookiesBtn = document.getElementById("clearCookiesBtn");
        const frame = document.getElementById("frame");
        const historyList = document.getElementById("historyList");
        const toastContainer = document.getElementById("toastContainer");
        const loading = document.getElementById("loading");

        const maxPrompts = 5;
        const promptCookieName = "savedPrompts";

        const showToast = (message) => {
            const toast = document.createElement("div");
            toast.className = "toast align-items-center text-white bg-success border-0 show";
            toast.role = "alert";
            toast.style.minWidth = "250px";
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>`;
            toastContainer.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        };

        submitBtn.addEventListener("click", function () {
            const prompt = promptInput.value.trim();
            if (!prompt) return;

            loading.style.display = "block";
            frame.src = `https://image.pollinations.ai/prompt/${encodeURIComponent(prompt)}?width=2048&height=2048&nologo=true`;

            frame.onload = () => {
                loading.style.display = "none";
                downloadBtn.disabled = false;
                savePromptBtn.disabled = false;
                shareBtn.disabled = false;
                showToast("Image generated successfully!");
            };
        });

        savePromptBtn.addEventListener("click", function () {
            const prompt = promptInput.value.trim();
            if (!prompt) return;

            const savedPrompts = JSON.parse(localStorage.getItem(promptCookieName)) || [];
            if (!savedPrompts.includes(prompt)) {
                savedPrompts.unshift(prompt);
                if (savedPrompts.length > maxPrompts) savedPrompts.pop();

                localStorage.setItem(promptCookieName, JSON.stringify(savedPrompts));
                showToast("Prompt saved!");
            } else {
                showToast("Prompt already exists!");
            }
        });

        downloadBtn.addEventListener("click", function () {
            if (frame.src) {
                const link = document.createElement("a");
                link.href = frame.src;
                link.download = "generated_image.png";
                link.click();
                showToast("Image downloaded!");
            }
        });

        shareBtn.addEventListener("click", function () {
            if (frame.src) {
                fetch(frame.src)
                    .then(res => res.blob())
                    .then(blob => {
                        const file = new File([blob], "generated_image.png", { type: blob.type });
                        if (navigator.share) {
                            navigator.share({
                                files: [file],
                                title: "AI Art Generator",
                                text: `Check out this amazing image I created using the prompt: "${promptInput.value}". Explore more at https://tryq8flix.com/aiGenerator.php`,
                            }).then(() => showToast("Image shared successfully!"))
                              .catch(() => showToast("Image sharing canceled."));
                        } else {
                            showToast("Sharing not supported on your browser.");
                        }
                    });
            }
        });

        // Helper function to handle historyList messages
        const showHistoryMessage = (message) => {
            historyList.style.display = 'block';
            historyList.innerHTML = message;
            setTimeout(() => {
                historyList.style.display = 'none';
            }, 3000);
        };

        viewHistoryBtn.addEventListener("click", function () {
            const savedPrompts = JSON.parse(localStorage.getItem(promptCookieName)) || [];
            if (savedPrompts.length === 0) {
                showHistoryMessage("<p>No prompts found!</p>");
                return;
            }
            
            historyList.style.display = 'block';
            historyList.innerHTML = "<h5>Prompt History:</h5>";
            savedPrompts.forEach((prompt, index) => {
                const btn = document.createElement("button");
                btn.textContent = `#${index + 1}: ${prompt}`;
                btn.className = "btn btn-outline-primary btn-sm d-block mt-2";
                btn.onclick = () => {
                    promptInput.value = prompt;
                    submitBtn.click();
                };
                historyList.appendChild(btn);
            });
            setTimeout(() => {
                historyList.style.display = 'none';
            }, 10000);
        });

        clearCookiesBtn.addEventListener("click", function () {
            localStorage.removeItem(promptCookieName);
            showHistoryMessage("<p>Cookies cleared!</p>");
            showToast("Cookies cleared!");
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>