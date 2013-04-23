
  prettyPrint();
  function update() {
    var opts = {};
    $('.opts input[min], .opts .color').each(function() {
      var val = $(this).hasClass("color") ? this.value : parseFloat(this.value);
      if($(this).hasClass("color")){
        val = "#" + val;
      }
      if(this.name.indexOf("lineWidth") != -1 ||
        this.name.indexOf("angle") != -1 ||
        this.name.indexOf("pointer.length") != -1){
        val /= 100;
      }else if(this.name.indexOf("pointer.strokeWidth") != -1){
        val /= 1000;
      }
      $('#opt-' + this.name.replace(".", "-")).text(val);
      if(this.name.indexOf(".") != -1){
      	var elems = this.name.split(".");
      	var tmp_opts = opts;
      	for(var i=0; i<elems.length - 1; i++){
      		if(!(elems[i] in tmp_opts)){
      			tmp_opts[elems[i]] = {};
      		}
      		tmp_opts = tmp_opts[elems[i]];
      	}
      	tmp_opts[elems[elems.length - 1]] = val;
      }else if($(this).hasClass("color")){
        // color picker is removing # from color values
      	opts[this.name] = "#" + this.value
        $('#opt-' + this.name.replace(".", "-")).text("#" + this.value);
      }else{
      	opts[this.name] = val;
      }
      if(this.name == "currval"){
      	// update current demo gauge
      	demoGauge.set(parseInt(this.value));
      	AnimationUpdater.run();
      }
    });
    $('#opts input:checkbox').each(function() {
      opts[this.name] = this.checked;
      $('#opt-' + this.name).text(this.checked);
    });
    demoGauge.animationSpeed = opts.animationSpeed;
    opts.generateGradient = true;
    demoGauge.setOptions(opts);
    demoGauge.ctx.clearRect(0, 0, demoGauge.ctx.canvas.width, demoGauge.ctx.canvas.height);
    demoGauge.render();
    if ($('#share').is(':checked')) {
      window.location.replace('#?' + $('form').serialize());
    }
  }
  function initGauge(){
    document.getElementById("class-code-name").innerHTML = "Gauge";
    demoGauge = new Gauge(document.getElementById("canvas-preview"));
    demoGauge.setTextField(document.getElementById("preview-textfield"));
    demoGauge.maxValue = 3000;
    demoGauge.set(1244);
  };
  function initDonut(){
    document.getElementById("class-code-name").innerHTML = "Donut";
    demoGauge = new Donut(document.getElementById("canvas-preview"));
    demoGauge.setTextField(document.getElementById("preview-textfield"));
    demoGauge.maxValue = 3000;
    demoGauge.set(1244);
  };
  $(function() {
    var params = {};
    var hash = /^#\?(.*)/.exec(location.hash);
    if (hash) {
      $('#share').prop('checked', true);
      $.each(hash[1].split(/&/), function(i, pair) {
        var kv = pair.split(/=/);
        params[kv[0]] = kv[kv.length-1];
      });
    }
    $('.opts input[min], #opts .color').each(function() {
      var val = params[this.name];
      if (val !== undefined) this.value = val;
      this.onchange = update;
    });
    $('.opts input[name=currval]').mouseup(function(){
    	AnimationUpdater.run();
    });

    $('.opts input:checkbox').each(function() {
      this.checked = !!params[this.name];
      this.onclick = update;
    });
    $('#share').click(function() {
      window.location.replace(this.checked ? '#?' + $('form').serialize() : '#!');
    });
    
    $("#type-select li").click(function(){
    	$("#type-select li").removeClass("active")
    	$(this).addClass("active");
    	var type = $(this).attr("type");
    	if(type=="donut"){
    		initDonut();
    		$("input[name=lineWidth]").val(10);
    		$("input[name=fontSize]").val(24);
    		$("input[name=angle]").val(35);

    		$("input[name=colorStart]").val("6F6EA0")[0].color.importColor();
    		$("input[name=colorStop]").val("C0C0DB")[0].color.importColor();
    		$("input[name=strokeColor]").val("EEEEEE")[0].color.importColor();

    		fdSlider.disable('input-ptr-len');
    		fdSlider.disable('input-ptr-stroke');
        $("#input-ptr-color").prop('disabled', true);

        selectGaguge1.set(1);
        selectGaguge2.set(3000);

    	}else{
    		initGauge();
    		$("input[name=lineWidth]").val(44);
    		$("input[name=fontSize]").val(41);
    		$("input[name=angle]").val(15);

    		$("input[name=colorStart]").val("6FADCF")[0].color.importColor();
    		$("input[name=colorStop]").val("8FC0DA")[0].color.importColor();
    		$("input[name=strokeColor]").val("E0E0E0")[0].color.importColor();

    		fdSlider.enable('input-ptr-len');
    		fdSlider.enable('input-ptr-stroke');
        $("#input-ptr-color").prop('disabled', false);
        selectGaguge1.set(3000);
        selectGaguge2.set(1) ;
    	}
    	fdSlider.updateSlider('input-line-width');
    	fdSlider.updateSlider('input-font-size');
    	fdSlider.updateSlider('input-angle');
    	$("#example").removeClass("donut");
    	$("#example").removeClass("gauge");
    	$("#example").addClass(type);
    	update();
    });

    selectGaguge1 = new Gauge(document.getElementById("select-1"));
    selectGaguge1.maxValue = 3000;
    selectGaguge1.set(1552);
    
    selectGaguge2 = new Donut(document.getElementById("select-2"));
    selectGaguge2.maxValue = 3000;
    selectGaguge2.set(1844);
    
    initGauge();
    update();
    
  });
