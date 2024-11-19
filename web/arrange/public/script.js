document.addEventListener('DOMContentLoaded', function () {
    const addButton = document.querySelector('button[type="button"]:not(.remove-button)');
    if (addButton) {
        addButton.addEventListener('click', addTimeSlot);
    }

    // 初期値としてAMの時間オプションを設定
    updateTimeOptions('AM', 'startTimeHour', 'startTimeMinute');
    updateTimeOptions('AM', 'endTimeHour', 'endTimeMinute'); // AMPM

    // AM/PM選択肢が変更されたときに開始時間のオプションを更新
    document.getElementById('startTimeOfDay').addEventListener('change', function () {
        updateTimeOptions(this.value, 'startTimeHour', 'startTimeMinute');
    });

    // 開始時間が変更されたときに終了時間のオプションを更新
    document.getElementById('startTimeHour').addEventListener('change', function () {
        updateTimeOptions(this.value, 'endTimeHour', 'endTimeMinute');
    });

    // イベントIDの確認とデバッグ
function debugEventID() {
    const eventID = document.getElementById('eventID').value; // EventIDがどこに保存されているか確認
    console.log('EventID:', eventID); // EventIDの値をデバッグ出力
    return eventID;
}
    // フォーム送信時に時刻を24時間形式に変換して送信
    document.getElementById('submitFormButton').addEventListener('click', function () {
        const startHour = document.getElementById('startTimeHour').value;
        const startPeriod = document.getElementById('startTimeOfDay').value;
        const startHour24 = convertTo24HourFormat(parseInt(startHour), startPeriod);

        const endHour = document.getElementById('endTimeHour').value;
        const endPeriod = document.getElementById('endTimeOfDay').value;
        const endHour24 = convertTo24HourFormat(parseInt(endHour), endPeriod);

        // フォームに送信する値を更新
        document.getElementById('startHour24').value = startHour24;
        document.getElementById('endHour24').value = endHour24;

        // フォームを送信
        document.forms[0].submit();
    });
});

// AM/PMを24時間形式に変換する関数
function convertTo24HourFormat(hour, period) {
    if (period === 'PM' && hour !== 12) {
        return hour + 12; // 12 PM以外は24時間形式に変換
    } else if (period === 'AM' && hour === 12) {
        return 0; // 12 AMは0時に変換
    }
    return hour;
}

// 時間オプションをAM/PMに基づいて更新
function updateTimeOptions(timeOfDay, timeSelectId, minuteSelectId) {
    const timeSelect = document.getElementById(timeSelectId);
    const minuteSelect = document.getElementById(minuteSelectId);
    timeSelect.innerHTML = '';
    minuteSelect.innerHTML = '';

    let startTime = (timeOfDay === 'PM') ? 12 : 0;
    let endTime = (timeOfDay === 'PM') ? 12 : 24;

    // 時間のオプションを追加
    for (let i = startTime; i < endTime; i++) {
        let option = document.createElement('option');
        option.value = i.toString().padStart(2, '0');
        option.text = i.toString().padStart(2, '0');
        timeSelect.appendChild(option);
    }

    // 分のオプションを15分刻みで追加
    for (let i = 0; i < 60; i += 15) {
        let option = document.createElement('option');
        option.value = i.toString().padStart(2, '0');
        option.text = i.toString().padStart(2, '0');
        minuteSelect.appendChild(option);
    }
}

// 時刻をフォーマット
function formatTime(hours, minutes) {
    return `${hours.padStart(2, '0')}:${minutes.padStart(2, '0')}`;
}

// 候補日時リスト
let timeSlots = [];

// 候補日時を追加
function addTimeSlot() {
    const startTimeOfDay = document.getElementById('startTimeOfDay').value;
    const endTimeOfDay = document.getElementById('endTimeOfDay').value;
    const startTime = formatTime(
        document.getElementById('startTimeHour').value,
        document.getElementById('startTimeMinute').value
    );
    const endTime = formatTime(
        document.getElementById('endTimeHour').value,
        document.getElementById('endTimeMinute').value
    );
    const date = document.getElementById('inputDate').value;

    if (!startTime || !endTime || !date) {
        alert('すべての時間、分、日付を入力してください。');
        return;
    }

    const startDate = `${date} ${startTimeOfDay} ${startTime}`;
    const endDate = `${date} ${endTimeOfDay} ${endTime}`;

    if (timeSlots.some(slot => slot.startDate === startDate && slot.endDate === endDate)) {
        alert('同じ候補日時が既に追加されています。');
        return;
    }

    timeSlots.push({ startDate, endDate });
    displayTimeSlots();
}

// 候補日時を表示
function displayTimeSlots() {
    const timeSlotContainer = document.getElementById('timeSlotContainer');
    timeSlotContainer.innerHTML = '';

    timeSlots.forEach((slot, index) => {
        const timeSlot = document.createElement('div');
        timeSlot.classList.add('timeSlot');
        timeSlot.innerHTML = `
            <p>候補日時: ${slot.startDate} ～ ${slot.endDate}</p>
        `;

        const removeButton = document.createElement('button');
        removeButton.textContent = '削除';
        removeButton.classList.add('removeButton');
        removeButton.onclick = function () {
            timeSlots.splice(index, 1);
            displayTimeSlots();
        };

        timeSlot.appendChild(removeButton);
        timeSlotContainer.appendChild(timeSlot);
    });
}
