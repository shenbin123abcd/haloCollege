;app.util=(function(){
    function formatTitle(str) {
        let strArr=str.split('|')
        return `${_.trim(strArr[0])}《${_.trim(strArr[1])}》`
    }
    return {
        formatTitle:formatTitle,
    };
}());


