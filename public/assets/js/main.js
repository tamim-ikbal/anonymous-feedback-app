//Coatext Color Toggle
mmCoatexColorToggle()
document.querySelector('select[name="texture"]').addEventListener('change',mmCoatexColorToggle)
function mmCoatexColorToggle(){
    let value = document.querySelector('select[name="texture"]').value;
    let textElement = document.querySelector('.coatex-color');
    if(60 == value){
        textElement.style.display = 'block';
    }else{
        textElement.style.display = 'none';
    }
}