document.addEventListener('DOMContentLoaded', function () {
    const addButton = document.querySelector('button[type="button"]:not(.remove-button)');
    
    // ボタンが押されたときに時間スロットを追加
    addButton.addEventListener('click', addTimeSlot);

    // 最初に時間帯を設定
    populateTimeOptions();
});

// 時間の動的に行う関数
function populateTimeOptions() {
    const startTimeSelect = document.getElementById("start_time");
    const endTimeSelect = document.getElementById("end_time");
    const startMinuteSelect = document.getElementById("start_time_minute");
    const endMinuteSelect = document.getElementById("end_time_minute");


    // 時間オプションを追加 (1〜12)
    for (let hour = 1; hour <= 12; hour++) {
        let startHourOption = document.createElement("option");
        let endHourOption = document.createElement("option");

        startHourOption.value = hour;
        endHourOption.value = hour;
        startHourOption.textContent = hour;
        endHourOption.textContent = hour;

        // 時間選択に追加
        startTimeSelect.appendChild(startHourOption);
        endTimeSelect.appendChild(endHourOption);
    }

    // 分のオプション（00, 15, 30, 45）
    [0, 15, 30, 45].forEach(minute => {
        let startMinuteOption = document.createElement("option");
        let endMinuteOption = document.createElement("option");

        // 分を設定
        startMinuteOption.value = String(minute).padStart(2, '0');
        endMinuteOption.value = String(minute).padStart(2, '0');

        startMinuteOption.textContent = String(minute).padStart(2, '0');
        endMinuteOption.textContent = String(minute).padStart(2, '0');

        // 分選択に追加
        startMinuteSelect.appendChild(startMinuteOption);
        endMinuteSelect.appendChild(endMinuteOption);
    });
}
 
// 時間スロットを追加する関数
function addTimeSlot() {
    const startHour = document.getElementById("start_time").value;
    const startMinute = document.getElementById("start_time_minute").value;
    const endHour = document.getElementById("end_time").value;
    const endMinute = document.getElementById("end_time_minute").value;

    // 時間帯と時間を組み合わせて表示
    const fullStartTime = `${startHour}:${startMinute}`;
    const fullEndTime = `${endHour}:${endMinute}`;
    const fullTimeSlot = `${fullStartTime} ～ ${fullEndTime}`;

    // 重複を防ぐために同じ内容の候補日がないか確認
    const existingSlots = Array.from(document.getElementsByClassName("time-slot"));
    const isDuplicate = existingSlots.some(slot => slot.textContent.includes(fullTimeSlot));

    if (!isDuplicate) {
        // 新しい時間帯を表示
        displayTimeSlot(fullTimeSlot);
    }
}

// 新しい時間帯の表示を行う関数
function displayTimeSlot(fullTimeSlot) {
    const timeSlotContainer = document.getElementById("time-slot-container");

    // 新しい要素を作成
    const timeSlot = document.createElement("div");
    timeSlot.classList.add("time-slot");
    timeSlot.innerHTML = `<p>候補日: ${fullTimeSlot}</p>`;

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
