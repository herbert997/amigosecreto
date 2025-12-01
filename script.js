let pessoas = [];

function addPessoa() {
    const nome = document.getElementById("nome").value.trim();
    if (nome === "") {
        alert("Insira um nome!");
        return;
    }

    pessoas.push(nome);
    document.getElementById("nome").value = "";
    atualizarLista();
}

function atualizarLista() {
    const ul = document.getElementById("lista");
    ul.innerHTML = "";

    pessoas.forEach(p => {
        const li = document.createElement("li");
        li.textContent = p;
        ul.appendChild(li);
    });
}

function sortear() {
    if (pessoas.length < 2) {
        alert("Cadastre pelo menos 2 pessoas!");
        return;
    }

    let sorteado = [...pessoas];
    let resultado = [];

    // Shuffle até não haver pessoa sorteando ela mesma
    let valido = false;

    while (!valido) {
        valido = true;
        embaralhar(sorteado);

        for (let i = 0; i < pessoas.length; i++) {
            if (pessoas[i] === sorteado[i]) {
                valido = false;
                break;
            }
        }
    }

    // Preencher resultado
    const ul = document.getElementById("resultado");
    ul.innerHTML = "";

    for (let i = 0; i < pessoas.length; i++) {
        const li = document.createElement("li");
        li.textContent = `${pessoas[i]} → ${sorteado[i]}`;
        ul.appendChild(li);
    }
}

function embaralhar(array) {
    for (let i = array.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [array[i], array[j]] = [array[j], array[i]];
    }
}
