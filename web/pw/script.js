document.addEventListener('DOMContentLoaded', () => {
    // イベントリスナーを初期化
    document.getElementById('btn').addEventListener('click', generatePasswords);

    // カスタム長さのフィールドの有効化/無効化
    document.querySelectorAll('input[name="passLength"]').forEach((elem) => {
        elem.addEventListener('change', function() {
            const lengthCustomInput = document.getElementById('lengthCustomInput');
            if (this.value === 'custom') {
                lengthCustomInput.disabled = false;  // カスタム選択で入力フィールドを有効化
            } else {
                lengthCustomInput.disabled = true;   // その他の選択で無効化
                lengthCustomInput.value = 4; // デフォルト値
            }
        });
    });

    // カスタム生成数のフィールドの有効化/無効化
    document.querySelectorAll('input[name="passQuantity"]').forEach((elem) => {
        elem.addEventListener('change', function() {
            const otherQuantityInput = document.getElementById('quantityOtherInput');
            if (this.value === 'other') {
                otherQuantityInput.disabled = false;  // カスタム選択で入力フィールドを有効化
            } else {
                otherQuantityInput.disabled = true;   // その他の選択で無効化
                otherQuantityInput.value = 8; // デフォルト値
            }
        });
    });
});

function generatePasswords() {
    // 長さを取得
    const lengthOptions = document.getElementsByName('passLength');
    let length = 12;  // デフォルト長
    for (const option of lengthOptions) {
        if (option.checked) {
            if (option.value === 'custom') {
                length = parseInt(document.getElementById('lengthCustomInput').value) || 8;
            } else {
                length = parseInt(option.value);
            }
        }
    }

    // オプションのチェックボックス状態を取得
    const includeLowercase = document.getElementById('includeLowercase').checked;
    const includeUppercase = document.getElementById('includeUppercase').checked;
    const includeNumbers = document.getElementById('includeNumbers').checked;
    const includeHyphen = document.getElementById('includeHyphen').checked;
    const includeUnderscore = document.getElementById('includeUnderscore').checked;
    const includeSymbols = document.getElementById('includeSymbols').checked;

    // 使用する文字セットを構築
    const charset = [
        ...(includeLowercase ? 'abcdefghijklmnopqrstuvwxyz'.split('') : []),
        ...(includeUppercase ? 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('') : []),
        ...(includeNumbers ? '0123456789'.split('') : []),
        ...(includeHyphen ? '-'.split('') : []),
        ...(includeUnderscore ? '_'.split('') : []),
        ...(includeSymbols ? '!#$%&()*+,-./:;<=>?@[\\]^_`{|}~'.split('') : []),
    ];

    // 生成するパスワード数を取得
    const quantityOptions = document.getElementsByName('passQuantity');
    let passwordCount = 10;  // デフォルトは10個
    for (const option of quantityOptions) {
        if (option.checked) {
            if (option.value === 'other') {
                passwordCount = parseInt(document.getElementById('quantityOtherInput').value) || 8;
            } else {
                passwordCount = parseInt(option.value);
            }
        }
    }

    // パスワード生成結果を表示するためにカラムをクリア
    const passwordColumn = document.getElementById('password-column');
    passwordColumn.innerHTML = ''; 

    // パスワードを生成する関数
    for (let i = 0; i < passwordCount; i++) {
        let password = '';
        for (let j = 0; j < length; j++) {
            const randomIndex = Math.floor(Math.random() * charset.length);
            password += charset[randomIndex];
        }
        const passwordBox = document.createElement('div');
        passwordBox.classList.add('password-box');
        passwordBox.innerHTML = `<span>${password}</span>`;
        passwordColumn.appendChild(passwordBox);
    }
}

// パスワードをクリップボードにコピーする関数
function copyPassword() {
    const passwordColumn = document.getElementById('password-column');
    const passwordText = passwordColumn.innerText;
    navigator.clipboard.writeText(passwordText).then(() => {
        alert('パスワードがクリップボードにコピーされました。');
    }).catch(err => {
        console.error('クリップボードへのコピーに失敗しました: ', err);
    });
}