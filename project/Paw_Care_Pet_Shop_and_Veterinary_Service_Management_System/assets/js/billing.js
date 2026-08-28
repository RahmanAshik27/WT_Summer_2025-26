document.addEventListener(
    "DOMContentLoaded",
    function () {

        // Screer Er Digital clock CLOCK
  
        const clock = document.getElementById( "checkoutClock" );
        const dateElement = document.getElementById( "checkoutDate" );

        function updateCheckoutTime() 
        {
            const now = new Date();

            if (clock) 
                {
                    clock.innerText = now.toLocaleTimeString();
                }

            if (dateElement) 
                {
                    dateElement.innerText = now.toLocaleDateString( undefined, { weekday: "long", year: "numeric", month: "long", day: "numeric" } );
                }
        }

        updateCheckoutTime();
        setInterval( updateCheckoutTime, 1000 );

        const deliveryOptions = document.querySelectorAll(".checkout-option");

        deliveryOptions.forEach(function (option) {

            const radio = option.querySelector('input[type="radio"]');
            radio.addEventListener("change", function () 
            {
                deliveryOptions.forEach(function (item) 
                {
                    item.classList.remove("selected-delivery");
                });

                option.classList.add("selected-delivery");

                showCheckoutToast( "Delivery method selected: " + radio.value );
            });
        });

        // Payment Section er kaj 

const paymentButtons = document.querySelectorAll(".payment-btn");
const selectedPaymentMethod = document.getElementById("selectedPaymentMethod");

paymentButtons.forEach(function (button)
{
    button.addEventListener("click", function ()
     {
        paymentButtons.forEach(function (item) {

            item.classList.remove("active");
        });

        button.classList.add("active");
        selectedPaymentMethod.value = button.dataset.payment;

        showCheckoutToast( "Payment method selected: " + button.dataset.payment );

    });
});
        const checkoutToast = document.getElementById("checkoutToast");
        const checkoutToastMessage = document.getElementById("checkoutToastMessage");

        let toastTimer;

        function showCheckoutToast(message)
         {
            checkoutToastMessage.innerText = message;
            checkoutToast.classList.add("show");
            clearTimeout(toastTimer);
            toastTimer = setTimeout(function () {checkoutToast.classList.remove("show");}, 2500);
        }
}   
);