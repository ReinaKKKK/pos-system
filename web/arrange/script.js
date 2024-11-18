document.addEventListener('DOMContentLoaded', function () {
    const addButton = document.querySelector('button[type="button"]:not(.remove-button)');
    if (addButton) {
        addButton.addEventListener('click', addTimeSlot);
    }

    updateTimeOptions('AM', 'startTimeHour', 'startTimeMinute');
    updateTimeOptions('AM', 'endTimeHour', 'endTimeMinute'); //AMPM

    document.getElementById('startTimeOfDay').addEventListener('change', function () {
        updateTimeOptions(this.value, 'startTimeHour', 'startTimeMinute');
    }); //選択された AM または PM に応じて開始時間のオプションを更新します

    document.getElementById('startTimeHour').addEventListener('change', function () {
        updateTimeOptions(this.value, 'endTimeHour', 'endTimeMinute');
    }); //開始時間を設定することで終了時間がそれよりも前にならないように。
});

// AMPMに基づいて時間オプションを設定
function updateTimeOptions(timeOfDay, timeSelectId, minuteSelectId) {
    const timeSelect = document.getElementById(timeSelectId);
    const minuteSelect = document.getElementById(minuteSelectId);
    timeSelect.innerHTML = '';
    minuteSelect.innerHTML = '';

    let startTime = (timeOfDay === 'PM') ? 12 : 0;
    let endTime = (timeOfDay === 'PM') ? 24 : 12;

    for (let i = startTime; i < endTime; i++) {
        let option = document.createElement('option');
        option.value = i.toString().padStart(2, '0');
        option.text = i.toString().padStart(2, '0');
        timeSelect.appendChild(option);
    }

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
