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
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #fff;
            background: linear-gradient(135deg, #6b8cce 0%, #9cc5f2 50%, #6b8cce 100%);
            background-size: 300% 300%;
            animation: moveBackground 15s infinite;
            min-height: 100vh;
        }

        @keyframes moveBackground {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .glass-card {
            background: rgba(255,255,255,0.13);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.18);
            padding: 30px 20px;
            margin: 40px auto;
            max-width: 800px;
            transition: box-shadow 0.3s;
        }
        .glass-card:hover {
            box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.45);
        }

        h1, h2, h6 {
            text-align: center;
            margin-top: 20px;
            text-shadow: 2px 2px 8px #000, 0 0 10px #6b8cce;
            letter-spacing: 1px;
        }

        h1 {
            font-size: 2.8rem;
            font-weight: bold;
            margin-bottom: 0;
        }
        h6 {
            font-size: 1.2rem;
            margin-bottom: 30px;
            color: #e0eaff;
        }

        label {
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .form-control {
            border-radius: 12px;
            border: none;
            padding: 1rem;
            font-size: 1.1rem;
            background: rgba(255,255,255,0.18);
            color: #222;
            box-shadow: 0 2px 8px rgba(107,140,206,0.08);
            transition: background 0.2s;
        }
        .form-control:focus {
            background: rgba(255,255,255,0.28);
            outline: none;
            box-shadow: 0 0 0 2px #6b8cce;
        }

        .model-btn {
            margin: 0 8px 12px 8px;
            min-width: 110px;
            font-weight: 600;
            border-radius: 18px;
            border: 2px solid #fff;
            background: rgba(255,255,255,0.12);
            color: #fff;
            transition: background 0.2s, color 0.2s, border 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px rgba(107,140,206,0.10);
            position: relative;
        }
        .model-btn.active, .model-btn:focus {
            background: linear-gradient(90deg, #6b8cce 60%, #9cc5f2 100%);
            color: #fff;
            border: 2px solid #9cc5f2;
            box-shadow: 0 0 0 3px #9cc5f2;
        }
        .model-btn:hover {
            background: #9cc5f2;
            color: #222;
            border: 2px solid #fff;
        }

        .btn {
            font-size: 1.1rem;
            border-radius: 14px;
            margin-bottom: 8px;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px rgba(107,140,206,0.10);
        }
        .btn-primary, .btn-success, .btn-warning, .btn-info, .btn-secondary, .btn-danger {
            font-weight: 600;
        }
        .btn-primary:hover, .btn-success:hover, .btn-warning:hover, .btn-info:hover, .btn-secondary:hover, .btn-danger:hover {
            filter: brightness(1.1);
            box-shadow: 0 4px 16px rgba(107,140,206,0.18);
        }

        #returnImage {
            margin-top: 18px;
        }
        #frame {
            width: 100%;
            border-radius: 18px;
            box-shadow: 0 4px 24px 0 rgba(31, 38, 135, 0.18);
            min-height: 320px;
            background: repeating-linear-gradient(135deg, #e0eaff 0 10px, #fff 10px 20px);
            object-fit: cover;
            transition: box-shadow 0.3s;
        }
        #frame[src=""] {
            min-height: 320px;
            background: repeating-linear-gradient(135deg, #e0eaff 0 10px, #fff 10px 20px);
        }

        #historyList {
            margin-top: 10px;
            background: rgba(255,255,255,0.10);
            border-radius: 12px;
            padding: 10px;
            color: #222;
        }

        .btn-outline-primary.btn-sm {
            margin-bottom: 4px;
        }

        footer {
            text-align: center;
            margin-top: 30px;
            color: #e0eaff;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }

        footer a {
            margin: 0 10px;
            color: #fff;
            text-decoration: none;
            font-size: 1.2rem;
            transition: color 0.2s;
        }

        footer a:hover {
            text-decoration: underline;
            color: #9cc5f2;
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
    <h1>AI Art</h1>
    <h6>Transform Your Imagination Into Stunning Art</h6>
    <div class="container-fluid">
        <div class="glass-card row text-center">
          <div class="col-12"><h3 class="text-center mb-4" style="color:#fff;text-shadow:1px 1px 8px #6b8cce;">Enter Your Prompt</h3></div>
            <div class="col-12 mb-3">
                <input name="prompt" id="prompt" value="" class="form-control" placeholder="Describe your art (e.g., a futuristic city)">
            </div>
            <div class="col-12 pt-2 pb-2">
                <label class="text-white d-block mb-2" style="font-size:1.1rem;"><i class="fas fa-rocket"></i> Select Model:</label>
                <button class="btn model-btn" data-model="normal"><i class="fas fa-magic"></i> Normal</button>
                <button class="btn model-btn" data-model="turbo"><i class="fas fa-bolt"></i> Turbo</button>
                <button class="btn model-btn" data-model="flux"><i class="fas fa-fire"></i> Flux</button>
            </div>
            <div class="col-12 pt-2 text-center">
                <button id="submitBtn" class="btn btn-primary w-100"><i class="fas fa-image"></i> Generate Image</button>
            </div>
            <div id="toastContainer" class="col-12 pt-2 text-center"></div>
            <div class="col-12"><hr style="border-color:#9cc5f2;"></div>
            <div class="col-12 pt-3" id="returnImage">
                <img id="frame" src="">
            </div>
            <div class="col-12 pt-3">
                <button id="downloadBtn" class="btn btn-success w-100 mb-2" disabled><i class="fas fa-download"></i> Download</button>
                <button id="shareBtn" class="btn btn-warning w-100 mb-2" disabled><i class="fas fa-share-alt"></i> Share</button>
                <button id="savePromptBtn" class="btn btn-info w-100 mb-2" disabled><i class="fas fa-save"></i> Save Prompt</button>
                <button id="viewHistoryBtn" class="btn btn-secondary w-100 mt-2"><i class="fas fa-history"></i> Prompts History</button>
                <button id="clearCookiesBtn" class="btn btn-danger w-100 mt-2"><i class="fas fa-trash"></i> Clear Prompts</button>
                <div id="historyList"></div>
            </div>
        </div>
    </div>
    <footer>
        <p>Share this website:</p>
        <a href="https://www.facebook.com/sharer/sharer.php?u=https://tryq8flix.com/aiGenerator.php" target="_blank"><i class="fab fa-facebook"></i> Facebook</a>
        <a href="https://twitter.com/intent/tweet?url=https://tryq8flix.com/aiGenerator.php" target="_blank"><i class="fab fa-twitter"></i> Twitter</a>
        <a href="https://wa.me/?text=Check out this amazing AI Art Generator: https://tryq8flix.com/aiGenerator.php" target="_blank"><i class="fab fa-whatsapp"></i> WhatsApp</a>
        <p>Courtesy of <a href="https://tryq8flix.com" target="_blank">TRYQ8FLiX</a></p>
    </footer>
    <div id="loading">
        <div class="spinner"></div>
        <p>Generating Your Art... Please Wait</p>
    </div>
    <script src="https://kit.fontawesome.com/7b2e1e5f2a.js" crossorigin="anonymous"></script>
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
            // Create blocking overlay
            const overlay = document.createElement("div");
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 2000;
            `;

            // Create centered toast
            const toast = document.createElement("div");
            toast.className = "toast align-items-center text-white bg-success border-0 show";
            toast.role = "alert";
            toast.style.cssText = `
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                min-width: 300px;
                z-index: 2001;
                opacity: 0;
                transition: opacity 0.5s ease-in-out;
            `;
            
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                </div>`;

            document.body.appendChild(overlay);
            document.body.appendChild(toast);
            
            // Fade in
            setTimeout(() => {
                toast.style.opacity = "1";
            }, 50);

            // Fade out and remove
            setTimeout(() => {
                toast.style.opacity = "0";
                overlay.style.opacity = "0";
                setTimeout(() => {
                    toast.remove();
                    overlay.remove();
                }, 500);
            }, 2500);
        };

        // Model button logic
        document.querySelectorAll(".model-btn").forEach(button => {
            button.addEventListener("click", function () {
                document.querySelectorAll(".model-btn").forEach(btn => btn.classList.remove("active"));
                this.classList.add("active");
            });
        });
        // Set default active model
        document.querySelector('.model-btn[data-model="normal"]').classList.add("active");

        submitBtn.addEventListener("click", function () {
            const prompt = promptInput.value.trim();
            const selectedModel = document.querySelector(".model-btn.active")?.getAttribute("data-model") || "normal";
            if (!prompt) return;

            loading.style.display = "block";
            const modelParam = selectedModel !== "normal" ? `&model=${selectedModel}` : "";
            frame.src = `https://image.pollinations.ai/prompt/${encodeURIComponent(prompt)}?width=2048&height=2048&nologo=true${modelParam}`;

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
            showToast("Prompts cleared!");
        });

        promptInput.addEventListener("keypress", function(event) {
            if (event.key === "Enter") {
                submitBtn.click();
            }
        });

        // Add input event for mobile devices
        promptInput.addEventListener("input", function(event) {
            if (event.inputType === "insertLineBreak") {
                event.preventDefault();
                submitBtn.click();
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>


