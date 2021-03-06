import {describe, it, assert} from "teevi/src/teevi.js"

describe('bootstrap-input-spinner', function () {
    it('Should display the spinner', function () {
        addInput()
        const $input = $("input[type='number']")
        $input.inputSpinner()
        $input.inputSpinner("destroy")
    })
})

function addInput() {
    var testContainer = document.getElementById("testContainer")
    var input = document.createElement("input")
    input.type = "number"
    testContainer.append(input)
}