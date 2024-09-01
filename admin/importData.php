<style>
    .modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
}

.modal-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    width: 400px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    position: relative;
    text-align: center;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}

label {
    margin-bottom: 10px;
    font-weight: bold;
    color: #4A148C;
    display: block;
}

input[type="file"] {
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
    width: 100%;
}

.modal-content button[type="submit"] {
    width: 100%;
}

@media (max-width: 600px) {
    .modal-content {
        width: 90%;
    }
}

.info {
    font-size: 12px;
    margin: 10px;
}
</style>
<div id="myModal" class="modal" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-content">
        <span class="close" aria-label="Close">&times;</span>
        <h2 id="modalTitle">Upload Student File</h2>
        <form action="uploadFile.php" method="post" enctype="multipart/form-data">
            <label for="file">Select the student file to upload:</label>
            <input type="file" name="file" id="file" required>
            <div class="info">
                *allowed type: csv <br>
                *max size: 2MB
            </div>
            <button type="submit">Upload File</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var modal = document.getElementById("myModal");
        var btn = document.getElementById("openModalBtn");
        var span = document.getElementsByClassName("close")[0];

        if (btn) {
            btn.onclick = function() {
                modal.style.display = "flex";
            }
        }

        if (span) {
            span.onclick = function() {
                modal.style.display = "none";
            }
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    });
</script>
