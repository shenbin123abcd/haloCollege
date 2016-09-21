;app.util=(function(){
    function formatTitle(str) {
        if(str.indexOf(/\||丨/g)>-1){
            let strArr=str.split(/\||丨/g);
            return `${_.trim(strArr[0])}《${_.trim(strArr[1])}》`
        }else{
            return str
        }
    }
    return {
        formatTitle:formatTitle,
    };
}());


