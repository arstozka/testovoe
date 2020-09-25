document.addEventListener('DOMContentLoaded', evt => {
    if (document.getElementById('new-anketa').length) {
        setFormHandlers()
    }
})

function setFormHandlers() {
    const form = document.querySelector('.form');
    const submit = form.querySelector('[type="submit"]');
    let buttons = form.querySelectorAll('.button');
    const avatarInput = form.querySelector('#avatar');
    Array.prototype.forEach.call(buttons, button => {
        button.addEventListener('click', function (event) {
            const element = this;
            if (element.getAttribute("type") === "submit") return false;
            const nextStep = element.getAttribute('data-next-step');
            if (!!nextStep) setFormStep(form, nextStep)
        })
    })
    avatarInput.addEventListener('change', function () {
        let parentNode = avatarInput.closest('.form__item');
        const maxSize = avatarInput.getAttribute('size');
        parentNode.classList.remove('form__item--invalid');
        if (this.files[0]) {
            let reader = new FileReader();
            if(maxSize && this.files[0].size>maxSize){
                console.log(maxSize);
                console.log(this.files[0].size);
                parentNode.classList.add('form__item--invalid');
                return false;
            }

            reader.addEventListener("load", function () {
                form.querySelector('[for="avatar"] span').style.backgroundImage = `url(${reader.result})`;
            }, false);
            reader.readAsDataURL(this.files[0]);
        }
    })
}

function setFormStep(form, stepId) {
    const fieldset = form.querySelectorAll('fieldset');
    fieldset.forEach(element => {
        element.classList.remove('current');
    })
    const step = document.getElementById(stepId);
    step.classList.add('current')
}
