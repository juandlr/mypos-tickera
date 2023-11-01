function updateAge() {
    day = jQuery("#day").val();
    month = jQuery("#month").val();
    year = jQuery("#year").val();
    if (day == "" || month == "" || year == "") {
        jQuery("#age").html("---");
    } else {
        var d = new Date();

        var currentmonth = d.getMonth() + 1;
        var currentday = d.getDate();
        var currentyear = d.getFullYear();
        age = currentyear - year;
        if (month > currentmonth || (month == currentmonth && day > currentday)) age--;
        jQuery("#age").val(age);
        jQuery("#agedisplay").html(age);
    }
}


(function ($) {


    // this object does all our price calculations
    var priceCalculator = {
        subtotal: 70.50,
        packagePrice: 342.40,
        qty: 1,
        thinAttire: 0,
        thickAttire: 0,
        total: 0,
        thinAttireValues : [48.20, 53.50, 58.90, 64.20],
        thickAttireValues : [85.60, 96.30, 101.70],

        init: function () {
            $('#membership-package').change(this.setPackage);
            $('#qty').change(this.setQty);
            $('#thin-attire').change(this.setThinAttire);
            $('#thick-attire').change(this.setThickAttire);
            this.calculateTotal();
        },
        setPackage: function (e) {
            var membershipType = $(this).val();
            switch (membershipType) {
                case '0':
                    priceCalculator.packagePrice = 20.30;
                    $('.qty').show();
                    $('#qty').removeAttr('disabled');
                    break;
                case '1':
                    priceCalculator.packagePrice = 235.40;
                    break;
                case '2':
                    priceCalculator.packagePrice = 28.90;
                    $('.qty').show();
                    $('#qty').removeAttr('disabled');
                    break;
                case '3':
                    priceCalculator.packagePrice = 342.40;
                default:
                    priceCalculator.packagePrice = 342.40;
                    break;
            }
            priceCalculator.calculateTotal();
        },

        setQty: function (e) {
            priceCalculator.Qty = $(this).val();
            priceCalculator.calculateTotal();
        },

        setThinAttire: function (e) {
            priceCalculator.thinAttire = $(this).val();
            priceCalculator.calculateTotal();
        },
        setThickAttire: function (e) {
            priceCalculator.thickAttire = $(this).val();
            priceCalculator.calculateTotal();
        },
        calculateTotal: function () {
            priceCalculator.total = (priceCalculator.qty * priceCalculator.packagePrice) + priceCalculator.subtotal + priceCalculator.thinAttireValues[priceCalculator.thinAttire] + priceCalculator.thickAttireValues[priceCalculator.thickAttire];
            console.log(priceCalculator.total);
            $('#total_span').text(priceCalculator.total.toFixed(2));
            $('#total').val(priceCalculator.total.toFixed(2));
        }
    }
    priceCalculator.init();

})(jQuery);