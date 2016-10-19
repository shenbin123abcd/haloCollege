import brandingPic from '../images/branding-pic.png'
import itemPic from '../images/item-pic.png'
import joinPic from '../images/join-us.png'
import hopePic from '../images/hope-you.png'
import wePic1 from '../images/we-pic1.png'
import wePic2 from '../images/we-pic2.png'

var Branding=React.createClass({
    getInitialState(){
      return{
          codeStatus:false,
          sendBtnStatus:false,
          timeOut:false,
          count:60,
      }
    },

    componentDidMount(){

    },
    handleInput(e){
        if(hb.validation.checkPhone(e)){
            this.setState({
                codeStatus:true
            })
        }else{
            this.setState({
                codeStatus:false
            })
        }
    },
    sendCode(e){
      let _this=this;
      if(this.state.codeStatus){
          $.ajax({
              url: '/courses/agentsCode',
              data: {
                  phone:e
              },
              method:'POST',
              success:function(res){
                  if(res.iRet==1){
                      _this.setState({
                          timeOut:true
                      });
                      var setint = setInterval(function(){
                          _this.state.count--
                          if (_this.state.count == 0) {
                              window.clearInterval(setint);
                              _this.setState({
                                  codeStatus:true,
                                  timeOut:false,
                                  count:60
                              })
                          }else{
                              _this.setState({
                                  count:_this.state.count
                              })
                          }
                      },1000);
                  }else{
                      hb.lib.weui.alert(res.info);
                  }
              },
              error:function(){
                  hb.lib.weui.alert('网络繁忙稍后再试');
              }
          })
      }
    },
    handleCodeInput(e){
        if(e){
            this.setState({
                sendBtnStatus:true
            })
        }else{
            this.setState({
                sendBtnStatus:false
            })
        }
    },
    handleSubmit(e){
      let _this=this;
      if(this.state.sendBtnStatus){
          $.ajax({
              url: '/courses/agentsApply',
              data: e,
              method: 'POST',
              success:function(res){
                  if(res.iRet==1){
                    _this.setState({
                        codeStatus:false,
                        sendBtnStatus:false,
                        timeOut:false,
                        count:60,
                    });
                    hb.lib.weui.alert(res.info);
                  }else{
                      hb.lib.weui.alert(res.info);
                  }
              },
              error:function(){
                  hb.lib.weui.alert('网络繁忙请稍后再试');
              }
          })
      }
    },
    render(){
        let screenHeight=window.screen.height;
        let renderStatus=()=>{
            let codeStyle,btnStyle='';
            if(this.state.codeStatus==false){
                codeStyle='send-message dark'
            }else{
                codeStyle='send-message orange'
            }

            if(this.state.sendBtnStatus==false){
                btnStyle='send-at-once dark'
            }else{
                btnStyle='send-at-once orange'
            }
            return{
                codeStyle,
                btnStyle,
            }
        }

        let renderCodeBtn=()=>{
            if(this.state.timeOut==false){
                return(
                    <div className={renderStatus().codeStyle} onClick={e=>this.sendCode($(this.refs.phone).val())}>获取验证码</div>
                )
            }else{
                return(
                    <div className={renderStatus().codeStyle}>{this.state.count}秒后重新发送</div>
                )
            }
        }
        return(
            <div className="branding-wrapper">
                <div className="pic-block" style={{height:screenHeight}}>
                    <img src={brandingPic} alt=""/>
                </div>
                <div className="item-list">
                    <img src={itemPic} alt=""/>
                </div>
                <div className="join-us">
                    <img src={joinPic} alt=""/>
                </div>
                <div className="hope-you">
                    <img src={hopePic} alt=""/>
                </div>
                <div className="form-block">
                    <div className="form-inner">
                        <div className="input-style dark">
                            <input className="form-control" type="text" placeholder="请输入手机号" ref="phone" onInput={e=>this.handleInput($(this.refs.phone).val())}/>
                            {renderCodeBtn()}
                        </div>
                        <div className="input-style">
                            <input className="form-control" type="text" placeholder="请输入验证码" ref='code' onInput={e=>this.handleCodeInput($(this.refs.code).val())}/>
                        </div>
                    </div>
                    <div className={renderStatus().btnStyle} onClick={e=>this.handleSubmit({
                        phone:$(this.refs.phone).val(),
                        code:$(this.refs.code).val(),
                    })}>
                        立即提交
                    </div>
                </div>
                <div className="weixin-block">
                    <div className="wrapper">
                        <div className="pic">
                            <img src={wePic1} alt=""/>
                        </div>
                        <div className="line"></div>
                        <div className="pic">
                            <img className='pic' src={wePic2} alt=""/>
                        </div>
                    </div>
                    <div className="text">即刻扫码添加微信号，申请成为<span className="sp">幻熊研习社分销商</span></div>
                </div>
            </div>
        )
    }
});

function mapStateToProps(state) {
    const { } = state
    return {

    }
}

export default ReactRedux.connect(mapStateToProps)(Branding)


