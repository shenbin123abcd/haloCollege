import {fetchConsiltItemsIfNeeded,fetchPanel} from '../actions/consult'
import PageLoading  from '../components/Common.Pageloading'


var CSSTransitionGroup = React.addons.CSSTransitionGroup;
var Panel=ReactBootstrap.Panel;
var Button=ReactBootstrap.Button;

var Consult=React.createClass({
    componentDidMount(){
        let {dispatch}=this.props;
        document.title='报名咨询';
        if(Modernizr.weixin&&Modernizr.ios){
            hb.hack.setTitle(document.title);
        }
        dispatch(fetchConsiltItemsIfNeeded());
    },
    handlePanel(e){
        let {dispatch}=this.props;
        dispatch(fetchPanel(e));
    },
    render(){
        let {consultData}=this.props;
        let isFetching=consultData.isFetching;
        let data=consultData.data;
        let renderPage=()=>{
            let _this=this;
            if(!data){
                var isNull=true
            }else if(data.length===0){
                var isEmpty =true
            }

            if (isFetching||isNull) {
                return <PageLoading key={1}/>
            }else if(isEmpty) {
                return (
                    <div>no data</div>
                )
            }else{
                //console.log(consultData);
                return(
                    <div className="consult-block">
                        <Item
                            data={data}
                            handlePanel={this.handlePanel}
                        >
                        </Item>
                    </div>
                )
            }
        }
        return(
            <div>
                <CSSTransitionGroup  transitionName="transition" component="div" transitionEnterTimeout={300} transitionLeaveTimeout={10}>
                    {renderPage()}
                </CSSTransitionGroup>
            </div>

        )
    }
});

var Item=React.createClass({
    render(){
        let data=this.props.data;
        let handlePanel=this.props.handlePanel;
        return(
            <div className="consult-list">
                {
                    data.map((n,i)=>{
                        let renderIcon=()=>{
                            if(n.status==false){
                                return{
                                    lineStyle:'line',
                                    iconStyle:'item-icon'
                                }
                            }else{
                                return{
                                    lineStyle:'line hidden',
                                    iconStyle:'item-icon rotate'
                                }
                            }
                        }
                        return(
                            <div className="consult-item" key={i}>
                                <div className="panel-button" onClick={e=>handlePanel(n.id)}>
                                    <span className="haloIcon haloIcon-question"></span>
                                    <div className="item-question">
                                        {n.q}
                                    </div>
                                    <div className={renderIcon().lineStyle}></div>
                                    <div className={renderIcon().iconStyle}>
                                        <span className='haloIcon haloIcon-arrow'></span>
                                    </div>
                                </div>
                                <Panel collapsible expanded={n.status}>
                                    <div className="paner-answer">
                                        {n.a}
                                    </div>
                                </Panel>
                            </div>
                        )
                    })
                }
            </div>
        )
    }
})



function mapStateToProps(state) {
    const { consultData } = state
    return{
        consultData,
    }

}

export default ReactRedux.connect(mapStateToProps)(Consult)
