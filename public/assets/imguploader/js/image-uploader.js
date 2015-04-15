function ImageUploader(opts) {
	var inst = this;
	
	var status = "ready";
				
	var img = opts.img;
	
	var jcropAPI = null;
	
	var extend = function (obj, extension) {
		if (!extension) return obj;
		if (!obj) obj = {};
		
		for (var i in extension)  {
			obj[i] = extension[i];
		}
		
		return obj
	}
	
	var uploaderAPI = new qq.FileUploaderBasic({
		params: extend({target_width: opts.targetWidth, target_height: opts.targetHeight}, opts.uploadParams),
		button: opts.uploadBtn[0],
		action: opts.uploadURL,
		allowedExtensions: opts.allowedExtensions,
		onSubmit:  function (id, fileName) {
			inst._loading();
		},		
		onComplete: function (id, fileName, resp) {		
			if (!resp.error && !resp.success) {
				alert("При загрузке возникла ошибка, пожалуйста, повторите попытку.");
				inst.remove();
				inst.setStatus("ready");
				return;			
			}
			
			if (resp.error) {			
				inst.remove();
				inst.setStatus("ready");
				return;
			}
			
			var shouldCrop = Math.abs(resp.width-opts.targetWidth)>5 || Math.abs(resp.height-opts.targetHeight)>5;							
			inst.display(resp.url, shouldCrop);
			!shouldCrop && opts.onChange && opts.onChange(resp);
		}

	});		
	
	opts.deleteBtn.bind("click", function (e) {
		if (!confirm("Уверены что хотите удалить фотографию?")) return;
				
		inst.remove();
		inst.setStatus("ready");
	});
	
	opts.cropBtn.bind("click", function (e) {
        inst.crop();	
	});	
	
	img.bind("load", function () {
		if (status != "loading-for-crop") return;
		
		if (jcropAPI) jcropAPI.destroy();
		
		
		
		var iw = img.width();
		var ih = img.height();		
		
		var s = Math.min(iw/opts.targetWidth, ih/opts.targetHeight, 1);
		
		var sw = s * opts.targetWidth;
		var sh = s * opts.targetHeight;		
		
		var sx = (iw - sw)/2;
		var sy = (ih - sh)/2;
		
		jcropAPI = $.Jcrop(img, {
			aspectRatio: opts.targetWidth / opts.targetHeight,
			resizable: true,
			minSize: [sw, sh],
			setSelect: [sx, sy, sx+sw, sy+sh]
		});
		
		inst.setStatus("cropping");
	});
	
	this.remove = function () {		
		if (jcropAPI) jcropAPI.destroy();	 
		jcropAPI = null;
			 
		img.attr("src", "").hide();
			
		opts.hidden.attr("value", "");	
		
		inst.setStatus("ready");
		opts.onChange && opts.onChange();	
	};
	
	this.status = function () {
		return status;	
	};	
	
	this.display = function (src, shouldCrop) {
		this.remove();
		
		if (!src) {
			inst.setStatus("ready");
			return; 
		}
	
		img.attr("src", src).show();
			
		if (!shouldCrop) {
			opts.hidden.attr("value", src);		
			inst.setStatus("ready");
			return;		
		}	
			
		inst.setStatus("loading-for-crop");
	}
	
	this.crop = function () {
		if (!jcropAPI) return;
		
		var sel = jcropAPI.tellSelect();
		jcropAPI.destroy();
		
		$.ajax({
			"url": opts.cropURL,
			"type": "POST",
			"dataType": "json",
			"data": extend({
				"target_width": opts.targetWidth,
				"target_height": opts.targetHeight,
				"x1": sel.x,
				"y1": sel.y,
				"x2": sel.x2,
				"y2": sel.y2,
				"url": img.attr("src")
			}, opts.cropParams),
			"success": function (data, textStatus, jqXHR) {
				if (data.error) {
					alert(data.error);
					return;
				}
								
				inst.display(data.url, false);
				opts.onChange && opts.onChange(data);
			},
			"error": function (jqXHR, textStatus, errorThrown) {
				alert("Возникла ошибка при обработке изображения.");
			}
		});
		
		this._loading();	
	}

	this._loading = function () {
		img.attr("src", opts.loaderURL).show();	
	};
	
	this.setStatus = function (value) {
		status = value;
			
		if (status == "cropping" || status == "loading-for-crop") {
			opts.uploadBtn.hide();	
		} else {
			opts.uploadBtn.show();
		}
		
		if (status == "cropping") {
			opts.cropBtn.show();
		} else {
			opts.cropBtn.hide();
		}
	}	
						
}
