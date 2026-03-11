<div id="input-code" class="hidden flex-col items-center w-full" onclick="goLast()">
    <section class="flex flex-row  justify-between w-fit">
        <input type="text" name="nb1" id="in1" size="1" tabindex="1" maxlength="1" pattern="[1-9]" placeholder="X" class="input-code- m-1 border-4 rounded-2xl border-vertClair w-12 h-18 text-4xl text-center">
        <input type="text" name="nb2" id="in2" size="1" tabindex="2" maxlength="1" pattern="[1-9]" placeholder="X" class="input-code- m-1 border-4 rounded-2xl border-vertClair w-12 h-18 text-4xl text-center">
        <input type="text" name="nb3" id="in3" size="1" tabindex="3" maxlength="1" pattern="[1-9]" placeholder="X" class="input-code- m-1 border-4 rounded-2xl border-vertClair w-12 h-18 text-4xl text-center">
        <img src="../../../images/logo/bootstrap_icon/dash.svg" alt="tiret" width="40" height="40">
        <input type="text" name="nb4" id="in4" size="1" tabindex="4" maxlength="1" pattern="[1-9]" placeholder="X" class="input-code- m-1 border-4 rounded-2xl border-vertClair w-12 h-18 text-4xl text-center">
        <input type="text" name="nb5" id="in5" size="1" tabindex="5" maxlength="1" pattern="[1-9]" placeholder="X" class="input-code- m-1 border-4 rounded-2xl border-vertClair w-12 h-18 text-4xl text-center">
        <input type="text" name="nb6" id="in6" size="1" tabindex="6" maxlength="1" pattern="[1-9]" placeholder="X" class="input-code- m-1 border-4 rounded-2xl border-vertClair w-12 h-18 text-4xl text-center">
    </section>
    <button id="valider" onclick="submit(<?= $idClient ?>)" class="border-vertClair border-2 rounded-xl w-40 h-14 cursor-pointer m-5" disabled>Vérifier</button>
</div>

<script>
const parent = document.getElementById("input-code")
const valider = document.getElementById("valider")
if (document.querySelector("body").classList.contains("show")){
    parent.style.display = "flex"
}
//const txt_key = document.getElementById("txt-key");

const eleFirst = document.getElementById("in1")


document.querySelectorAll(".input-code-").forEach((input, idx, inputs) => {

    input.value = ""
    index = input.tabIndex
    const reg = /^[0-9]$/
    let idPrev="in"+(index-1)
    let idNext="in"+(index+1)
    
    const elePrev = document.getElementById(idPrev)
    const eleNext = document.getElementById(idNext)
    

    input.addEventListener('paste', (e) =>{
        e.preventDefault()
        let txt = e.clipboardData.getData("text")
        for (i=0;i<6;i++){
            if (reg.test(txt[i])){
                document.querySelectorAll(".input-code-")[i].value = txt[i]
                document.querySelectorAll(".input-code-")[5].focus()
            }else{
                document.querySelectorAll(".input-code-")[i].value = ""
            }
        }
        toggleValider()
    })

    input.addEventListener('keydown', (e) => {
        if (e.key == "Backspace" && input.value == "" && idx > 0){ 
            elePrev.focus()
            
        }
        if (e.key == "Backspace"){
            valider.setAttribute("disabled", "true")
        }
        if (e.key == "ArrowLeft" || e.key == "ArrowRight"){
            e.preventDefault()
        }
        if (e.key == "ArrowRight" && idx < inputs.length-1){

            eleNext.focus()
            eleNext.selectionStart = eleNext.selectionEnd = eleNext.value.length
        }
        if (e.key == "ArrowLeft" && idx > 0){

            elePrev.focus()
            elePrev.selectionStart = elePrev.selectionEnd = elePrev.value.length
        }

    })

    input.addEventListener('input', (e) =>{
        if (!reg.test(input.value)){
            input.value = ""
            return
        }

        if (idx < inputs.length-1){
            eleNext.focus()
        }

        toggleValider()
    })

    input.addEventListener('click', () => {
        goLast()
    })

});

function toggleValider(){
    console.log(recup_code())
    if (test_code(recup_code()) == true){
        valider.removeAttribute("disabled")
    }else{
        valider.setAttribute("disabled", "true")
    }
}

function recup_code(){
    let code = ""
    for (i=1;i<7;i++){
        char = document.getElementById("in"+i).value
        code += char
    }
    return code
}

function test_code(code){
    return code.length == 6
}

function goFirst(){
    console.log("first")
    eleFirst.focus()
}

function goLast(){
    code = recup_code()
    size = (code.length<6)?code.length+1:6
    console.log(code+" "+size)
    let eleLast = document.getElementById("in"+size)
    eleLast.focus()
}



</script>