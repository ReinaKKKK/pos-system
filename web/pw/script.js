document.getElementById('btn').addEventListener('click', generatePasswords);

// 数量オプションの変更イベントにリスナーを追加
const quantityOptions = document.getElementsByName('passQuantity');
quantityOptions.forEach(option => {
    option.addEventListener('change', function () {
        const customQuantityInput = document.getElementById('customQuantity');
        if (this.value === 'other') {
            customQuantityInput.disabled = false; // 有効化
        } else {
            customQuantityInput.disabled = true; // 無効化
            customQuantityInput.value = this.value; // 選択された数量で入力値を更新
        }
    });
});

// 長さオプションの変更イベントにリスナーを追加
const lengthOptions = document.getElementsByName('passLength');
lengthOptions.forEach(option => {
    option.addEventListener('change', function () {
        const customLengthInput = document.getElementById('customLength');
        if (this.value === 'other') {
            customLengthInput.disabled = false; // 有効化
            customLengthInput.value = ''; // 入力値をリセット
        } else {
            customLengthInput.disabled = true; // 無効化
            customLengthInput.value = this.value; // 選択された長さで入力値を更新
        }
    });
});

function generatePasswords() {
    // パスワードの長さを取得
    const lengthOptions = document.getElementsByName('passLength');
    let length = 12; // デフォルトの長さ
    for (const option of lengthOptions) {
        if (option.checked) {
            if (option.value === 'other') {
                length = parseInt(document.getElementById('customLength').value) || 12;
            } else {
                length = parseInt(option.value);
            }
        }
    }

    // パスワードの文字セットを構築
    const includeLowercase = document.getElementById('includeLowercase').checked;
    const includeUppercase = document.getElementById('includeUppercase').checked;
    const includeNumbers = document.getElementById('includeNumbers').checked;
    const includeHyphen = document.getElementById('includeHyphen').checked;
    const includeUnderscore = document.getElementById('includeUnderscore').checked;
    const includeSymbols = document.getElementById('includeSymbols').checked;

    const charset = [
        ...(includeLowercase ? 'abcdefghijklmnopqrstuvwxyz'.split('') : []),
        ...(includeUppercase ? 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('') : []),
        ...(includeNumbers ? '0123456789'.split('') : []),
        ...(includeHyphen ? '-'.split('') : []),
        ...(includeUnderscore ? '_'.split('') : []),
        ...(includeSymbols ? '!#$%&()*+,-./:;<=>?@[\\]^_`{|}~'.split('') : []),
    ];
    
    // パスワードの数を取得
    const quantityOptions = document.getElementsByName('passQuantity');
    let passwordCount = 10; // デフォルトは10
    for (const option of quantityOptions) {
        if (option.checked) {
            if (option.value === 'other') {
                passwordCount = parseInt(document.getElementById('customQuantity').value) || 10;
            } else {
                passwordCount = parseInt(option.value);
            }
        }
    }

    // 前の結果をクリア
    document.getElementById('column1').innerHTML = ''; 
    document.getElementById('column2').innerHTML = '';

    // パスワードを生成して表示
    for (let i = 0; i < passwordCount; i++) {
        const password = generatePassword(charset, length);
        const passwordDiv = document.createElement('div');
        passwordDiv.classList.add('password-box'); // スタイルを適用
        passwordDiv.textContent = password;

        // カラムに追加
        if (i % 2 === 0) {
            document.getElementById('column1').appendChild(passwordDiv);
        } else {
            document.getElementById('column2').appendChild(passwordDiv);
        }
    }
}

function generatePassword(charset, length) {
    let password = '';
    for (let i = 0; i < length; i++) {
        const randomIndex = Math.floor(Math.random() * charset.length);
        password += charset[randomIndex];
    }
    return password;
}
