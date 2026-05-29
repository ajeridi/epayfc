function doubleToStringFormatted(number) {
            return number.toLocaleString('fr-FR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
}