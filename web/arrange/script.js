document.addEventListener('DOMContentLoaded', function () {
    const addButton = document.getElementById('btn');
    
    // ボタンが押されたときに時間スロットを追加
    addButton.addEventListener('click', addTimeSlot);
});

// 時間スロットの追加を動的に行う関数
function addTimeSlot() {
    const timeSlotsContainer = document.getElementById("timeSlots");

    // 新しい日付と時間スロットの入力欄を作成
    const newSlot = document.createElement("div");
    newSlot.classList.add("time-slot");

    newSlot.innerHTML = `
        <label>日付:</label>
        <input type="date" name="dates[]" required>
        <label>From:</label>
        <input type="time" name="start_times[]" required>
        <label>To:</label>
        <input type="time" name="end_times[]" required>
        <button type="button" class="remove-button" onclick="removeTimeSlot(this)">削除</button><br>
    `;

    // 作成した要素を追加
    timeSlotsContainer.appendChild(newSlot);
}

// 時間スロットを削除する関数
function removeTimeSlot(button) {
    button.parentElement.remove();
}
