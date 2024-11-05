const DEFAULT_PASSWORD_COUNT = 10; // デフォルトのパスワード数
const DEFAULT_OTHER_QUANTITY = 8;   // カスタム入力のデフォルト値
const DEFAULT_LENGTH = 12;           // デフォルトのパスワード長
const LOWERCASE_CHARS = 'abcdefghijklmnopqrstuvwxyz';
const UPPERCASE_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
const NUMERIC_CHARS = '0123456789';
const HYPHEN_CHAR = '-';
const UNDERSCORE_CHAR = '_';
const DEFAULT_CUSTOM_LENGTH = 4;      // カスタム長のデフォルト値

document.addEventListener('DOMContentLoaded', () => {
    generatePasswords();

    document.getElementById('btn').addEventListener('click', generatePasswords);

    document.querySelectorAll('input[name="passLength"]').forEach((elem) => {
        elem.addEventListener('change', function() {
            const lengthCustomInput = document.getElementById('lengthCustomInput');
            if (this.value === 'custom') {
                lengthCustomInput.disabled = false;
            } else {
                lengthCustomInput.disabled = true;
                lengthCustomInput.value = DEFAULT_CUSTOM_LENGTH;
            }
        });
    });

    document.querySelectorAll('input[name="passQuantity"]').forEach((elem) => {
        elem.addEventListener('change', function() {
            const otherQuantityInput = document.getElementById('quantityOtherInput');
            if (this.value === 'other') {
                otherQuantityInput.disabled = false;
            } else {
                otherQuantityInput.disabled = true;
                otherQuantityInput.value = DEFAULT_OTHER_QUANTITY;
            }
        });
    });
});

function generatePasswords() {
    const lengthOptions = document.getElementsByName('passLength');
    let length = DEFAULT_LENGTH;

    for (const option of lengthOptions) {
        if (option.checked) {
            if (option.value === 'custom') {
                length = parseInt(document.getElementById('lengthCustomInput').value) || DEFAULT_LENGTH;
            } else {
                length = parseInt(option.value);
            }
        }
    }

    const includeLowercase = document.getElementById('includeLowercase').checked;
    const includeUppercase = document.getElementById('includeUppercase').checked;
    const includeNumbers = document.getElementById('includeNumbers').checked;
    const includeHyphen = document.getElementById('includeHyphen').checked;
    const includeUnderscore = document.getElementById('includeUnderscore').checked;
    const includeSymbols = document.getElementById('includeSymbols').checked;

    const charset = [
        ...(includeLowercase ? LOWERCASE_CHARS.split('') : []),
        ...(includeUppercase ? UPPERCASE_CHARS.split('') : []),
        ...(includeNumbers ? NUMERIC_CHARS.split('') : []),
        ...(includeHyphen ? HYPHEN_CHAR.split('') : []),
        ...(includeUnderscore ? UNDERSCORE_CHAR.split('') : []),
        ...(includeSymbols ? document.getElementById('customSymbols').value.split('') : []),
    ];

    const quantityOptions = document.getElementsByName('passQuantity');
    let passwordCount = DEFAULT_PASSWORD_COUNT;

    for (const option of quantityOptions) {
        if (option.checked) {
            if (option.value === 'other') {
                passwordCount = parseInt(document.getElementById('quantityOtherInput').value) || DEFAULT_OTHER_QUANTITY;
            } else {
                passwordCount = parseInt(option.value);
            }
        }
    }

    const passwordColumn = document.getElementById('password-column');
    passwordColumn.innerHTML = '';

    for (let i = 0; i < passwordCount; i++) {
        let password = '';

        if (includeLowercase) {
            password += LOWERCASE_CHARS[Math.floor(Math.random() * LOWERCASE_CHARS.length)];
        }
        if (includeUppercase) {
            password += UPPERCASE_CHARS[Math.floor(Math.random() * UPPERCASE_CHARS.length)];
        }
        if (includeNumbers) {
            password += NUMERIC_CHARS[Math.floor(Math.random() * NUMERIC_CHARS.length)];
        }
        if (includeHyphen) {
            password += HYPHEN_CHAR;
        }
        if (includeUnderscore) {
            password += UNDERSCORE_CHAR;
        }

        const customSymbols = document.getElementById('customSymbols').value;
        if (includeSymbols && customSymbols) {
            password += customSymbols[Math.floor(Math.random() * customSymbols.length)];
        }

        while (password.length < length) {
            const randomIndex = Math.floor(Math.random() * charset.length);
            password += charset[randomIndex];
        }

        // シャッフルを適用
        password = shuffleArray(password.split('')).join('');

        const passwordBox = document.createElement('div');
        passwordBox.classList.add('password-box');

        const passwordSpan = document.createElement('span');
        passwordSpan.textContent = password;
        passwordBox.appendChild(passwordSpan);

        const copyButton = document.createElement('button');
        copyButton.classList.add('copy-btn');
        copyButton.innerHTML = '📋';
        copyButton.onclick = () => copyPassword(password);
        passwordBox.appendChild(copyButton);

        passwordColumn.appendChild(passwordBox);
    }
}

function shuffleArray(array) {
    for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
    return array;
}

function copyPassword(password) {
    navigator.clipboard.writeText(password).then(() => {
        alert('パスワードがクリップボードにコピーされました。');
    }).catch(err => {
        console.error('クリップボードへのコピーに失敗しました: ', err);
    });
}
