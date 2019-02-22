export default {
    computed: {
        getLabelPosition(){
            if(this.isMobile){
                return 'top';
            }else{
                return 'left';
            }
        }
    }
}