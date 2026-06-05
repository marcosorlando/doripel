const PREPOSICOES = ["da", "do", "das", "dos", "a", "e", "o", "os", "as", "de"];

$(function () {
    $("form.form_capitalize input[type='text']").blur(function () {
        let cp_value = capitalize($(this).val());
        $(this).val(cp_value);
    });

    //Call function *lowercase* for lowercase input[type=email]
    $("form.form_capitalize input[type='email']").keyup(function () {
        let email = lowercase($(this).val());
        $(this).val(email);
    });

    $("form.form_capitalize textarea:not(.no_capitalize)").blur(function () {
        let cp_value = capitalizeTextarea($(this).val());
        $(this).val(cp_value);
    });

});

/*################
 ##### FUNÇÕES #####
 ################*/
//TEXT FUNCTIONS


/**
 * @param {*} str
 * @param {boolean} force
 */
function ucfirst(str, force)
{
    str = force ? str.toLowerCase() : str;
    return str.replace(/(\b)([a-zA-Z])/, function (firstLetter) {
        return firstLetter.toUpperCase();
    });
}

/**
 *
 * @param s
 * @returns {boolean}
 */
function abreviacao(s)
{
    return /^([A-Z]\.)+$/.test(s);
}

function numeralRomano(s)
{
    return /^M{0,4}(CM|CD|D?C{0,3})(XC|XL|L?X{0,3})(IX|IV|V?I{0,3})$/.test(s);
}

function capitalize(texto)
{
    if (!texto) {
        return texto;
    }

    return texto
    .split(' ')
    .map((palavra) => formatWord(palavra))
    .join(' ');
}

function capitalizeTextarea(texto)
{
    if (!texto) {
        return texto;
    }

    const ABBR_DOT = '\x07';
    const LETTER_REGEX = /[A-Za-zÀ-ÖØ-öø-ÿ]/;

    let safeText = texto.replace(/((?:[A-Z]\.){2,})/g, (match) => match.replace(/\./g, ABBR_DOT));
    let result = '';
    let capitalizeNext = true;

    for (let i = 0; i < safeText.length; i++) {
        const char = safeText[i];

        if (char === ABBR_DOT) {
            result += '.';
            capitalizeNext = false;
            continue;
        }

        if (char === '.' || char === ';') {
            result += char;

            const nextChar = safeText[i + 1];
            if (nextChar && !/\s/.test(nextChar) && nextChar !== '.' && nextChar !== ';' && nextChar !== ABBR_DOT) {
                result += ' ';
            }

            capitalizeNext = true;
            continue;
        }

        if (capitalizeNext && LETTER_REGEX.test(char)) {
            result += char.toUpperCase();
            capitalizeNext = false;
            continue;
        }

        if (!/\s/.test(char)) {
            capitalizeNext = false;
        }

        result += char;
    }

    return result;
}
function formatWord(palavra)
{
    if (!palavra) {
        return palavra;
    }

    if (abreviacao(palavra) || numeralRomano(palavra)) {
        return palavra;
    }

    const lower = palavra.toLowerCase();

    if (PREPOSICOES.includes(lower)) {
        return lower;
    }

    return lower.charAt(0).toUpperCase() + lower.slice(1);
}

function lowercase(str)
{
    return str.toLowerCase();
}
