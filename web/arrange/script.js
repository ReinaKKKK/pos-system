document.addEventListener('DOMContentLoaded', function () {
    const addButton = document.querySelector('button[type="button"]:not(.remove-button)');
    
    // ボタンが押されたときに時間スロットを追加
    addButton.addEventListener('click', addTimeSlot);

    // 最初に時間帯を設定
    populateTimeOptions();
});

// 時間帯選択を更新する関数
function updateTimeOptions(timeOfDay, timeSelectId, minuteSelectId) {
    const timeSelect = document.getElementById(timeSelectId);
    const minuteSelect = document.getElementById(minuteSelectId);
    timeSelect.innerHTML = '';  // 既存のオプションをリセット
    minuteSelect.innerHTML = ''; // 既存の分オプションをリセット

    // 午前と午後で異なる時間を選べるようにする
    let startTime = 0;
    let endTime = 12;
    
    if (timeOfDay === 'PM') {
        startTime = 12;
        endTime = 24;
    }
    
    // 時間のオプションを追加
    for (let i = startTime; i < endTime; i++) {
        let option = document.createElement('option');
        option.value = (i < 10) ? '0' + i : i; // 時間を2桁で表示
        option.text = (i < 10) ? '0' + i : i;
        timeSelect.appendChild(option);
    }

    // 分のオプションを追加 (00, 15, 30, 45)
    for (let i = 0; i < 60; i += 15) {
        let option = document.createElement('option');
        option.value = (i < 10) ? '0' + i : i;
        option.text = (i < 10) ? '0' + i : i;
        minuteSelect.appendChild(option);
    }
}

// イベント候補を追加する関数
function addTimeSlot() {
    const startTimeOfDay = document.getElementById('start-time-of-day').value;
    const endTimeOfDay = document.getElementById('end-time-of-day').value;
    const startTime = document.getElementById('start_time').value;
    const startTimeMinute = document.getElementById('start_time_minute').value;
    const endTime = document.getElementById('end_time').value;
    const endTimeMinute = document.getElementById('end_time_minute').value;
    const date = document.getElementById('inputDate').value;  // 日付を取得

    // 入力値を検証
    if (!startTime || !startTimeMinute || !endTime || !endTimeMinute || !date) {
        alert('すべての時間、分、日付を入力してください。');
        return;
    }

    // 時間帯と時間の組み合わせを整える
    const startDate = date + ' ' + startTimeOfDay + ' ' + startTime + ':' + startTimeMinute;
    const endDate = date + ' ' + endTimeOfDay + ' ' + endTime + ':' + endTimeMinute;

    // 新しい候補日時を表示
    displayTimeSlot(startDate, endDate);
}

// 新しい時間帯の表示を行う関数
function displayTimeSlot(startDate, endDate) {
    const timeSlotContainer = document.getElementById("time-slot-container");

    // 新しい要素を作成
    const timeSlot = document.createElement("div");
    timeSlot.classList.add("time-slot");
    timeSlot.innerHTML = `<p>選択された日付: ${startDate}</p><p>候補日時: ${startDate} ～ ${endDate}</p>`;

    // 削除ボタンを作成して追加
    const removeButton = document.createElement("button");
    removeButton.textContent = "削除";
    removeButton.classList.add("remove-button");
    removeButton.onclick = function () {
        timeSlotContainer.removeChild(timeSlot);
    };
    timeSlot.appendChild(removeButton);

    // コンテナに新しい候補日を追加
    timeSlotContainer.appendChild(timeSlot);
}

// 時間スロットを削除する関数
function removeTimeSlot(button) {
    button.parentElement.remove();
}

// ページ読み込み時に時間帯選択を初期化
window.onload = function() {
    updateTimeOptions('AM', 'start_time', 'start_time_minute');
    updateTimeOptions('AM', 'end_time', 'end_time_minute');

    // 開始時間帯が変更されたときに時間オプションを更新
    document.getElementById('start-time-of-day').addEventListener('change', function() {
        updateTimeOptions(this.value, 'start_time', 'start_time_minute');
    });

    // 終了時間帯が変更されたときに時間オプションを更新
    document.getElementById('end-time-of-day').addEventListener('change', function() {
        updateTimeOptions(this.value, 'end_time', 'end_time_minute');
    });
};
