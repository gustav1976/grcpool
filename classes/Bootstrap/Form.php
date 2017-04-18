<?php
class Bootstrap_Form {
	private $_onSubmit;
	private $_method = 'post';
	public function setMethod($s) {
		$this->_method = strtolower($s);
	}
	public function setOnSubmit($s) {$this->_onSubmit = $s;}
	private $_id;
	public function setId($s) {$this->_id = $s;}
	private $_buttons;
	private $_buttonsOffset;
	public function setButtons($buttons,$offset = 3) {
		$this->_buttons = $buttons;	
		$this->_buttonsOffset = $offset;
	}
	private $_action;
	public function setAction($s) {$this->_action = $s;}
	private $_fields = array();
	public function addHr() {
		array_push($this->_fields,new Bootstrap_Hr());	
	}
	public function addField(Bootstrap_FormInput $input) {
		array_push($this->_fields,$input);	
	}
	public function wrap($content) {
		return '
			'.$this->_getOpenTag().'
			'.$content.'
			'.$this->_getButtons().'					
			'.$this->_getCloseTag().'
		';
	}
	private function _getCloseTag() {return '</form>';}
	private function _getOpenTag() {
		if ($this->_method == 'post') {
			return '<form id="'.$this->_id.'" '.($this->_onSubmit?'onsubmit="'.$this->_onSubmit.'"':'').' enctype="multipart/form-data" method="post" action="'.$this->_action.'" class="form-horizontal">';
		} else {
			return '<form id="'.$this->_id.'" '.($this->_onSubmit?'onsubmit="'.$this->_onSubmit.'"':'').' method="get" action="'.$this->_action.'" class="form-horizontal">';
		}
	}
	private function _getButtons() {
		if ($this->_buttons != "") {
			return '
				<div class="form-group" style="margin-top:25px;">
					<div style="padding-top:20px;border-top:1px solid #dddddd;" class="col-sm-offset-'.$this->_buttonsOffset.'">'.$this->_buttons.'</div>
				</div>
			';
		}
	}
	public function render() {
		$result = '';
		$result .= $this->_getOpenTag();
		foreach ($this->_fields as $field) {
			$result .= $field->render();	
		}
		$result .= '
			'.$this->_getButtons().'
			'.$this->_getCloseTag().'
			<script>
				function readURL(input,id) {
			    	if (input.files && input.files[0]) {
						var reader = new FileReader();
			 	        reader.onload = function (e) {
							var img = new Image;
							if (e.target.result.indexOf("application/pdf") == -1) {
								img.onload = function() {
					            	$("#"+id).attr("src", e.target.result);//.css("max-width",img.width+"px");
								};
								img.src = reader.result;
							} else {
				            	$("#"+id).attr("src","/Assets/icons48/pdf.png");
							}
						};
			            reader.readAsDataURL(input.files[0]);
			        }
			    }				
			</script>
		';
		return $result;
	}
}
class Bootstrap_Hr {
	public function render() {
		return '<hr/>';
	}
}
class Bootstrap_ReCaptchaInput extends Bootstrap_FormInput {
	private $_siteKey = '';
	public function setSiteKey($s) {$this->_siteKey = $s;}
	public function render() {
		return '
			<div id="reCaptchaGroup" class="form-group">
				<label for="areYouHuman" class="col-sm-'.$this->getLabelCols().' control-label">Human?</label>
				<div class="col-xs-'.$this->getInputSize().'">
					<div class="g-recaptcha" data-sitekey="'.$this->_siteKey.'"></div>
				</div>
			</div>
		';
	}
}
class Bootstrap_CaptchaInput extends Bootstrap_FormInput {
	private $_store = '';
	public function setStore($s) {$this->_store = $s;}
	public function __construct() {
		$this->setId('areYouHuman');
		$this->setLabel('Confirm Code');
	}
	public function render() {
		$static = new Bootstrap_StaticInput();
		$static->setDefault('<img src="/ajax/captcha?store='.$this->_store.'&cache='.rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).'"/>');		
		return '
			<div class="form-group" id="'.$this->getId().'Group">
				<label for="'.$this->getId().'" class="col-sm-'.$this->getLabelCols().' control-label">'.$this->getLabel().'</label>
				<div class="col-sm-'.$this->getInputSize().'">
					<input value="" type="text" class="form-control" id="'.$this->getId().'" name="'.$this->getId().'">
				</div>
				<p id="'.$this->getId().'Help" class="help-block">enter the code below</p>
			</div>
			'.$static->render().'
		';		
	}
}
class Bootstrap_HiddenInput extends Bootstrap_FormInput {
	public function render() {
		return '<input type="hidden" name="'.$this->getId().'" id="'.$this->getId().'" value="'.htmlspecialchars($this->getDefault()).'"></input>';
	}
}
class Bootstrap_RFMInput extends Bootstrap_FormInput {
	private $_rfmLink;
	private $_rfmVersion = '9.10.2';
	public function setRfmLink($s) {$this->_rfmLink = $s;}
	public function render() {
		return '
			<div class="form-group" id="'.$this->getId().'Group">
				<label for="'.$this->getId().'" class="col-sm-'.$this->getLabelCols().' control-label">'.$this->getLabel().'</label>
				<div class="col-sm-'.$this->getInputSize().'">
					<div class="input-group">
						<input class="form-control" id="'.$this->getId().'" name="'.$this->getId().'" type="text" value="'.htmlspecialchars($this->getDefault()).'"/>
						<span class="input-group-btn">
							<button href="/rfm/'.$this->_rfmLink.'/'.$this->_rfmVersion.'/filemanager/dialog.php?type=0&field_id='.$this->getId().'&relative_url=1&akey=ifgsfvsfrgvsfhnvg8y9rt5634t77wvsbnhsvy7yvnhvvnh8vh7y52" class="btn fancybox fancybox.iframe" type="button">Select</button>
						</span>
					</div>
				</div>
				<div class="col-sm-2">
					<img class="img-responsive img-zoom" style="height:30px;'.($this->getDefault()&&is_file(BASE_DIR.$this->getDefault())?'':'display:none;').'" src="'.$this->getDefault().'" id="'.$this->getId().'Preview"/>									
				</div>
			</div>				
		';
	}
}
class Bootstrap_TextInput extends Bootstrap_FormInput {
	//$webPage->appendHead('
	//<link href="/Assets/datepicker/20130911/bootstrap-datetimepicker.min.css" rel="stylesheet">
	//<script src="/Assets/datepicker/20130911/bootstrap-datetimepicker.min.js"></script>
	
	//<link href="/Assets/datepicker/20150924/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
	//<script src="/Assets/moment/20150924/min/moment.min.js"></script>
	//<script src="/Assets/datepicker/20150924/build/js/bootstrap-datetimepicker.min.js"></script>
	
	//!!!!!!!!!!!!! BOOTSTRAP_TIME_PICKER_INCL !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	
	private $_password = false;
	public function setPassword($b) {$this->_password = $b;}
	private $_timePicker = '';
	public function setTimePicker($s) {$this->_timePicker = $s;} 
		//$input->setTimePicker('pickTime:false');
		// $input->setTimePicker('format:"MM/DD/YYYY",keyBinds:null');
	public function getTimePicker() {return $this->_timePicker;}
	private $_placeholder;
	public function setPlaceholder($s) {$this->_placeholder = $s;}
	public function getPlaceholder() {return $this->placeholder;}
	private $_maxSize;
	public function setMaxSize($i) {$this->_maxSize = $i;}
	public function getMaxSize() {return $this->_maxSize;}
	private $_readOnly = false;
	public function setReadOnly($b) {$this->_readOnly = $b;}
	public function getReadOnly() {return $this->_readOnly;}
	private $_addonButton = '';
	private $_addonButtonId = '';
	public function setButtonAddon($label,$id='') {$this->_addonButton = $label;$this->_addonButtonId = $id;}
	
	public function render() {
		return '
			<div class="form-group" id="'.$this->getId().'Group">
				<label for="'.$this->getId().'" class="col-sm-'.$this->getLabelCols().' control-label">'.$this->getLabel().'</label>
				<div class="col-sm-'.$this->getInputSize().'">
					'.($this->_timePicker!=''?'<div class="input-group date" id="'.$this->getId().'TimePicker">':'').'
						'.($this->_addonButton!=''?'<div class="input-group">':'').'
							<input value="'.htmlspecialchars($this->getDefault()).'" type="'.($this->_password?'password':'text').'" class="form-control '.($this->getClass()).'" id="'.$this->getId().'" '.($this->getMaxSize()!=''?'maxlength="'.$this->getMaxSize().'"':'').' name="'.$this->getId().'" placeholder="'.$this->_placeholder.'" '.($this->getReadOnly()?'readonly':'').' '.($this->getDisabled()?'disabled="disabled"':'').'>
							'.($this->_addonButton!=''?'
								'.($this->_addonButtonId!=''?'
									<span class="input-group-btn">
										<button id="'.$this->_addonButtonId.'" class="btn btn-info" type="button">
											'.$this->_addonButton.'
										</button>
									</span>
								':'
									<span class="input-group-addon">'.$this->_addonButton.'</span>
								').'
							</div>
						':'').'
						'.($this->_timePicker!=""?'<span class="input-group-addon"><span class="fa fa-calendar"></span></span>':'').'
					'.($this->_timePicker!=''?'</div>':'').'
					'.($this->getHelp()!=''?'<p id="'.$this->getId().'Help" class="help-block">'.$this->getHelp().'</p>':'').'							
				</div>
				'.($this->_timePicker!=""?'<script>$("#'.$this->getId().'TimePicker").datetimepicker({'.$this->_timePicker.'});</script>':'').'
			</div>
		';
	}
}
class Bootstrap_RadioGroup extends Bootstrap_FormInput {
	private $_radios = array();
	private $_type = 'stacked';
	public function addRadio(Bootstrap_RadioInput $input) {
		array_push($this->_radios,$input);
	}
	public function render() {
		$result = '';
		if ($this->_type == 'stacked') {
			$result .= '
				<div class="form-group" id="'.$this->getId().'Group">
					<label class="col-sm-'.$this->getLabelCols().' control-label">'.$this->getLabel().'</label>
					<div class="col-sm-'.$this->getInputSize().'">
			';
			foreach ($this->_radios as $idx => $box) {
				$result .= '
					<div class="radio">
						<label>
							<input type="radio" name="'.$this->getId().'" id="'.$this->getId().'_'.$idx.'" value="'.$box->getValue().'" '.($box->getValue()==$box->getDefault()?'checked="checked"':'').'></input>
							'.$box->getLabel().'
						</label>
					</div>
				';
			}
			$result .= '</div></div>';
		}
		return $result;
	}	
}
class Bootstrap_RadioInput extends Bootstrap_FormInput {
	private $_value;
	public function setValue($s) {$this->_value = $s;}
	public function getValue() {return $this->_value;}
}
class Bootstrap_CheckboxGroup extends Bootstrap_FormInput {
	private $_checkboxes = array();
	public function addCheckbox(Bootstrap_CheckboxInput $checkbox) {
		array_push($this->_checkboxes,$checkbox);	
	}
	public function render() {
		$result = '';
		$result .= '<div class="form-group" id="'.$this->getId().'Group"><label class="col-sm-'.$this->getLabelCols().' control-label">'.$this->getLabel().'</label>';
		foreach ($this->_checkboxes as $box) {
			$result .= '<label style="margin-left:15px;margin-right:10px;" class="checkbox-inline"><input '.($box->getValue()==$box->getDefault()?'checked="checked"':'').' type="checkbox" id="'.$box->getId().'" value="'.$box->getValue().'"/> '.$box->getLabel().'</label>';
		}
		$result .= '</div>';
		return $result;
	}
}
class Bootstrap_CheckboxInput extends Bootstrap_FormInput {
	private $_value;
	public function setValue($s) {$this->_value = $s;}
	public function getValue() {return $this->_value;}
	public function render() {
		return '
			<div class="form-group" id="'.$this->getId().'Group">
				<div class="col-sm-offset-'.$this->getLabelCols().' col-sm-'.$this->getInputSize().'">
					<div class="checkbox">
						<label for="'.$this->getId().'">
							<input type="checkbox" id="'.$this->getId().'" name="'.$this->getId().'" value="'.$this->getValue().'" '.($this->getDefault() === $this->getValue()?'checked="checked"':'').'/>
							<span style="font-weight:bold;">'.$this->getLabel().'</span>
						</label>
					</div>
				</div>
			</div>
		';
		
		return '
			<div class="form-group" id="'.$this->getId().'Group">
				<label for="'.$this->getId().'" class="col-sm-'.$this->getLabelCols().' control-label">'.$this->getLabel().'</label>
				<div class="col-sm-'.$this->getInputSize().'">
					<input type="checkbox" id="'.$this->getId().'" name="'.$this->getId().'" value="'.$this->getValue().'" '.($this->getDefault() === $this->getValue()?'checked="checked"':'').'/>
				</div>
			</div>
		';
	}		
}
class Bootstrap_RawInput extends Bootstrap_FormInput {
	private $_raw;
	public function setRaw($str) {$this->_raw = $str;}
	public function render() {
		return $this->_raw;
	}	
}
class Bootstrap_SelectInput extends Bootstrap_FormInput {
	private $_onChange;
	public function setOnChange($str) {
		$this->_onChange = $str;
	}
	private $_options;
	public function setOptions($arr) {$this->_options = $arr;}
	private $_readOnly = false;
	public function setReadOnly($b) {$this->_readOnly = $b;}
	public function getReadOnly() {return $this->_readOnly;}
	public function render() {
		return '
			<div class="form-group" id="'.$this->getId().'Group">
				<label for="'.$this->getId().'" class="col-sm-'.$this->getLabelCols().' control-label">'.$this->getLabel().'</label>
				<div class="col-sm-'.$this->getInputSize().'">
					<select class="form-control" id="'.$this->getId().'" name="'.$this->getId().'" onchange="'.$this->_onChange.'" '.($this->getReadOnly()?'readonly':'').' '.($this->getDisabled()?'disabled="disabled"':'').'>
						'.$this->getOptions($this->_options,$this->getDefault()).'						
					</select>
				</div>					
				'.($this->getHelp()!=''?'<p id="'.$this->getId().'Help" class="help-block">'.$this->getHelp().'</p>':'').'																		
			</div>
		';
	}
}
class Bootstrap_StaticInput extends Bootstrap_FormInput {
	private $_p = true;
	public function setP($b) {$this->_p = $b;}
	public function render() {
		return '
			<div class="form-group">
				<label class="col-sm-'.$this->getLabelCols().' control-label">'.$this->getLabel().'</label>
				<div class="col-sm-'.$this->getInputSize().'">
				'.($this->_p?'
					<p id="'.$this->getId().'" class="form-control-static">'.$this->getDefault().'</p>
				':'
					'.$this->getDefault().'
				').'
				</div>					
			</div>
		';
	}	
}
class Bootstrap_TextAreaInput extends Bootstrap_FormInput {
	private $_html = false;
	public function setHtml($b) {$this->_html = $b;}
	public function getHtml() {return $this->_html;}
	public $_height = '200';
	public function setHeight($i) {$this->_height = $i;}
	public function getHeight() {return $this->_height;}
	public function render() {
		return '
			<div id="'.$this->getId().'Group" class="form-group">
				<label class="col-sm-'.$this->getLabelCols().' control-label">'.$this->getLabel().'</label>
				<div class="col-sm-'.($this->_html?12-$this->getLabelCols():$this->getInputSize()).'">
					<textarea class="form-control" name="'.$this->getId().'" id="'.$this->getId().'" style="width:100%;height:'.$this->_height.'px;">'.($this->_html?$this->getDefault():htmlspecialchars($this->getDefault())).'</textarea>
					'.($this->_html?'
						<script>
							tinymce.init({
								selector:"#'.$this->getId().'",
								theme: "modern",
						    	plugins: [
						         	"advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
						         	"searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
						         	"save table contextmenu directionality emoticons template paste textcolor"
						   		],
						   		content_css: "/Assets/bootstrap/3.0.0/css/bootstrap.min.css",
						   		toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink image | print preview media fullpage | forecolor backcolor"
							});
						</script>		
					':'').'
				</div>
			</div>
		';
	}
}
class Bootstrap_FileInput extends Bootstrap_FormInput {
	private $_preview;
	public function setPreview($previewHtml) { $this->_preview = $previewHtml; }
	public function render() {
		return '
			<div class="form-group" id="'.$this->getId().'Group">
				<label for="'.$this->getId().'" class="col-sm-'.$this->getLabelCols().' control-label">'.$this->getLabel().'</label>
				<div class="col-sm-'.$this->getInputSize().'">
				    <input type="file" id="'.$this->getId().'" name="'.$this->getId().'"/>
					'.($this->getDefault() != ""?'
					   <label class="checkbox-inline"><input value="delete" name="'.$this->getId().'Delete" id="'.$this->getId().'Delete" type="checkbox"> Delete existing image</label>
					':'').'
				</div>		
				'.($this->getHelp() != ''?'<p id="'.$this->getId().'Help" class="help-block">'.$this->getHelp().'</p>':'').'							
			</div>
			'.($this->_preview != ''?'
				<div class="form-group">
					<label class="col-sm-'.$this->getLabelCols().' control-label"></label>
					<div class="col-sm-'.$this->getInputSize().'">
						'.$this->_preview.'
					</div>		
				</div>
			':'').'
		';
	}
}
class Bootstrap_ImageInput extends Bootstrap_FormInput {
	public function render() {
		return '
			<div class="form-group" id="'.$this->getId().'Group">
				<label for="'.$this->getId().'" class="col-sm-'.$this->getLabelCols().' control-label">'.$this->getLabel().'</label>
				<div class="col-sm-1">
					<img class="img-responsive" src="'.$this->getDefault().'" id="'.$this->getId().'Preview"/>
				</div>
				<div class="col-sm-'.$this->getInputSize().'">
					<div class="inputWrapper" style="display:inline-block;margin-top:5px;">
					    <i class="fa fa-file"></i> Choose File <input onchange="readURL(this,\''.$this->getId().'Preview\')" class="fileInput" type="file" id="'.$this->getId().'" name="'.$this->getId().'"/>
					</div>
					'.($this->getDefault() != ""?'
				  	<div>
					   <label class="checkbox-inline"><input value="delete" name="'.$this->getId().'Delete" id="'.$this->getId().'Delete" type="checkbox"> Delete existing image</label>
					</div>
					':'').'
				</div>		
			</div>
		';
	}
}
abstract class Bootstrap_FormInput {
	private $_disabled = false;
	public function setDisabled($b) {$this->_disabled = $b;}
	public function getDisabled() {return $this->_disabled;}
	private $_default = '';
	public function setDefault($s) {$this->_default = $s;}
	public function getDefault() {return $this->_default;}
	private $_inputSize = 5;
	public function setInputSize($cols) {$this->_inputSize = $cols;}
	public function getInputSize() {return $this->_inputSize;}
	private $_labelCols = 3;
	public function getLabelCols() {return $this->_labelCols;}
	private $_class;
	public function setClass($s) {$this->_class = $s;}
	public function getClass() {return $this->_class;}
	private $_id;
	public function setId($s) {$this->_id = $s;}
	public function getId() {return $this->_id;}
	private $_help;
	public function setHelp($s) {$this->_help = $s;}
	public function getHelp() {return $this->_help;}
	private $_label;
	public function setLabel($s,$cols=3) {
		$this->_label = $s;
		$this->_labelCols = $cols;
	}
	public function getLabel() {return $this->_label;}
	
	public function getOptions($options,$default="") {
		$r = "";
		if (!is_array($options)) {
			if (strstr($options,"RANGE:")) {
				$options = substr($options,6);
				$range = explode("-",$options);
				$options = array();
				if ($range[0] < $range[1]) {
					for ($i = $range[0]; $i<= $range[1]; $i++) {
						$options[$i] = $i;
					}
				} else {
					for ($i = $range[0]; $i>= $range[1]; $i--) {
						$options[$i] = $i;
					}
				}
			} else if (strstr($options,"RANGEEVEN:")) {
				$options = substr($options,10);
				$range = explode("-",$options);
				$options = array();
				if ($range[0] < $range[1]) {
					for ($i = $range[0]; $i<= $range[1]; $i++) {
						if ($i % 2 == 0) {
							$options[$i] = $i;
						}
					}
				} else {
					for ($i = $range[0]; $i>= $range[1]; $i--) {
						if ($i % 2 == 0) {
							$options[$i] = $i;
						}
					}
				}
			} else if (strstr($options,"RANGEODD:")) {
				$options = substr($options,9);
				$range = explode("-",$options);
				$options = array();
				if ($range[0] < $range[1]) {
					for ($i = $range[0]; $i<= $range[1]; $i++) {
						if ($i % 2 != 0) {
							$options[$i] = $i;
						}
					}
				} else {
					for ($i = $range[0]; $i>= $range[1]; $i--) {
						if ($i % 2 != 0) {
							$options[$i] = $i;
						}
					}
				}
			} else {
				$newOptions = explode(",",$options);
				$options = array();
				for ($i = 0; $i < count($newOptions); $i++) {
					$options[$newOptions[$i]] = $newOptions[$i];
				}
			}
		}
		foreach ($options as $value => $option) {
			if (is_array($option)) {
				$r .= '<optgroup label="'.$option['label'].'">';
				foreach ($option['options'] as $oid => $oval) {
					$checked = '';
					if ((String)$oid === (String)$default) {
						$checked = 'selected="selected"';
					}
					$r .= '<option '.$checked.' value="'.$oid.'">'.$oval.'</option>';
				}
				$r .= '</optgroup>';
			} else {
				$checked = "";
				if (is_array($default)) {
					if (array_search($value,$default) === false) {
	
					} else {
						$checked = 'selected="selected"';
					}
				} else {
					if ((String)$value === (String)$default) {
						$checked = 'selected="selected"';
					}
				}
				$r .= '<option value="'.$value.'" '.$checked.'>'.$option.'</option>';
			}
		}
		return $r;
	}
	public function render() {}
}