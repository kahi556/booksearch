/*  _/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/ 

    
    Powered by . kerry
    
    http://202.248.69.143/~goma/
    
    ����u���E�U :: IE4+, NN6+, Gecko, Opera7+
    
    
_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/  */




function init()
{
    // ID �̓o�^
    // ID �̓N���[�g�Ŋ���A�����w��� , �R���}�ŋ�؂�
    var idList = new Array(
        "p0" ,
        "p1" ,
        "p2" ,
        "p3" ,
        "p4" ,
        "p5" ,
        "p6" ,
        "p7" ,
        "p8" ,
        "p9" ,
        "p10" ,
        "p11" ,
        "p12" ,
        "p13" ,
        "p14" ,
        "p15" ,
        "p16" ,
        "p17" ,
        "p18" ,
        "p19" ,
        "p20" ,
        "p21" ,
        "p22"
    );

    // �����X�s�[�h�@�i�@1000 = 1�b �j
    var intervalTime = 20;
    
    // �������� �i �P�� = �s�N�Z�� �j
    var directLength = 5;
    
    
    
    
    // ----- �ȉ��v���O���� ------ //
    
    
    if (!(document.getElementById || document.all) ||
        (document.body && document.body.style.overflow == undefined)) return ; 
    var o, oList = new Array();

    for (var i in idList)
    {
        o = document[document.all ? "all": "getElementById"](idList[i]);
        oList[idList[i]] = {
            obj     : o, 
            height  : o.offsetHeight,
            width   : o.offsetWidth,
            flag    : false
        };
        with (o.style)
        {   
            width =
            height = "1px";
            visibility =
            overflow = "hidden"
        }
    }

    opnClz = function (_oId, _direc, _oX, _oY)
    {
        if ((_oId = oList[_oId]))
        {
            with(_oId.obj.style)
            {
                visibility = "visible";
                if (_direc == "x") height = _oId.height+ "px";
                else width = _oId.width+ "px";
                
                if (_oX && _oY)
                {
                    position = "absolute";
                    top  = _oY;
                    left = _oX;
                }
            }
            _oId.direc = _direc == "x"? "width": "height";
            
            if (_oId.timeId)
            {
                clearInterval(_oId.timeId);
                _oId.flag = !_oId.flag;
            }
            else
                _oId.len = _oId.flag? _oId[_oId.direc] : 0;
           
            var drcLen = _oId.flag? Math.round(directLength* 2)* -1 : directLength;
            
            var opcz = function()
            {
                _oId.len += drcLen;
                
                if (_oId[_oId.direc] > _oId.len && _oId.len > 0)
                {
                    _oId.obj.style[_oId.direc] = _oId.len+ "px";
                }
                else
                {   
                    _oId.obj.style[_oId.direc] = (!_oId.flag ? _oId[_oId.direc] :1)+ "px";
                    if (_oId.flag) with(_oId.obj.style)
                    {
                        width  = 
                        height = "1px";
                        visibility = "hidden";
                    }
                    _oId.flag = !_oId.flag;
                    _oId.timeId = clearInterval(_oId.timeId);
                }
            } // END opcz()

            _oId.timeId = setInterval(opcz, intervalTime);
        }
    } // END opnClz()
}

function opnClz(){};



onload = init;

/* _/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/ 

�����y�[�W�ǂݍ��݊���������炤�Ȃ�

onload = init;

��������

init();

�Ƃ���B
�������邱�Ƃœǂݍ��݂�҂����X�O�ɔ��f�����B

_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/ */





