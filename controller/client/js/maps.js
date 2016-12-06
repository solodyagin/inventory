/*
 * WebUseOrg3 - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

// получаем список ТМЦ на всем заводе
function GetArrayEq(orgid) {
	$.get(route + 'controller/server/map/getjsonlisteq.php?fix=1', {orgid: orgid}, function (e) {
		zx = JSON.parse(e);
	});
}

function UpdateChosen() {
	for (var selector in config) {
		$(selector).chosen({width: '100%'});
		$(selector).chosen(config[selector]);
	}
}

// Получаем список помещений в выбранной организации
function GetListPlaces(orgid, placesid) {
	url = route + 'controller/server/map/getlistplacesmap.php?orgid=' + orgid + '&placesid=' + placesid + '&addnone=false';
	$.get(url, function (data) {
		$('#sel_pom').html(data);
		UpdateChosen();
	});
}

function loadmap(imgmap) {
	// Как только будет загружен API и готов DOM, выполняем инициализацию
	$('#map').html('');
	ymaps.ready(init);
	function init() {
		// Создаем декартову систему координат, на которую будет проектироваться карта.
		// Определяем границы области отображения в декартовых координатах.
		var myProjection = new ymaps.projection.Cartesian([
			[-1, -1], // координаты левого нижнего угла
			[1, 1]    // координаты правого верхнего угла
		]);
		// Создадим собственный слой карты:
		eqLayer = function () {
			return new ymaps.Layer(
					// Зададим функцию, преобразующую номер тайла и уровень масштабирования
							// в URL до тайла на нашем хостинге
									function (tile, zoom) {
										return 'photos/maps/' + zoom + '-' + tile[1] + '-' + tile[0] + '-' + imgmap;
									}
							);
						};

				// Добавим конструктор слоя в хранилище слоёв под ключом my#eq
				ymaps.layer.storage.add('my#eq', eqLayer);
				// Создадим новый тип карты, состоящий только из нашего слоя тайлов,
				// и добавим его в хранилище типов карты под ключом my#eq
				ymaps.mapType.storage.add('my#eq', new ymaps.MapType(
						'План помещений',
						['my#eq']
						));
				// Создаем карту в заданной системе координат.
				var myMap = new ymaps.Map('map', {
					center: [0, 0],
					zoom: 1,
					behaviors: ['default', 'scrollZoom'],
					// Указываем ключ нашего типа карты
					type: 'my#eq'
				}, {
					maxZoom: 3, // Максимальный коэффициент масштабирования для заданной проекции.
					minZoom: 1, // Максимальный коэффициент масштабирования
					projection: myProjection
				});

				myMap.container.fitToViewport();

				myMap.controls.add(new ymaps.control.ZoomControl());
				myMap.controls.add('mapTools');
				bounds = myMap.getBounds();
				// добавляем ТМЦ на карту
				if ($('#grpom').prop('checked')) {
					slpom = $('#splaces :selected').val();
				} else {
					slpom = 'null';
				}

				$.get(route + 'controller/server/map/getjsonlisteq.php?fix=1', // сначала получаем список
						{orgid: defaultorgid, selpom: slpom},
						function (e) {
							zx = JSON.parse(e); // парсим JSON в массив
							myCollection = new ymaps.GeoObjectCollection();
							PlaceTMC($('#splaces :selected').val());
						});
				// сохранение перемещений
				var myButton = new ymaps.control.Button({
					data: {content: 'Сохранить'}},
						{selectOnClick: false}
				);
				myButton.events
						.add('click', function () {
							myCollection.each(function (ob) {
								cr = ob.geometry.getBounds();
								$.get(route + 'controller/server/map/savemap.php?fix=1', {eqid: ob.properties.get('balloonContentFooter'), coor: cr},
										function (data) {
											//alert('Успешно сохранено!');
										});
							});
						});
				myMap.controls.add(myButton, {top: 5, left: 100});

				// различные функции
				//
				function PlaceTMC() {
					myCollection.removeAll();
					for (var i = 0; i < zx.rows.length; i++) {
						xx = zx.rows[i].cell[5];
						yy = zx.rows[i].cell[4];
						if ($('#stmetka').prop('checked')) {
							icontxt = '';
						} else {
							icontxt = zx.rows[i].cell[3] + '<br>' + zx.rows[i].cell[2];
						}

						if (zx.rows[i].cell[21] == 1) {
							cl = 'twirl#redStretchyIcon';
						} else {
							cl = 'twirl#greenStretchyIcon'
						}

						p = zx.rows[i].cell[22];
						if (p != '') {
							photo = '<br><img src=photos/' + p + ' height="50">';
						} else {
							photo = '';
						}

						var myGeoObject = new ymaps.GeoObject({
							geometry: {type: 'Point', coordinates: [xx, yy, xx, yy]},
							properties: {
								hintContent: zx.rows[i].cell[3],
								balloonContentFooter: zx.rows[i].cell[1],
								balloonContentHeader: zx.rows[i].cell[3],
								balloonContentBody: zx.rows[i].cell[2] + photo,
								iconContent: icontxt}
						}, {preset: cl, draggable: $('#moveme').prop('checked')});
						myCollection.add(myGeoObject);
					}
					myMap.geoObjects.add(myCollection);
				}

				// группировка по помещениям - клик
				$('#grpom').click(function () {
					if ($('#grpom').prop('checked')) {
						slpom = $('#splaces :selected').val();
					} else {
						slpom = 'null';
					}
					$.get(route + 'controller/server/map/getjsonlisteq.php?fix=1', // сначала получаем список
							{orgid: defaultorgid, selpom: slpom},
							function (e) {
								zx = JSON.parse(e); // парсим JSON в массив
								PlaceTMC();
							});
				});
				// можем/нет перемещать
				$('#moveme').click(function () {
					if ($('#grpom').prop('checked')) {
						slpom = $('#splaces :selected').val();
					} else {
						slpom = 'null';
					}

					$.get(route + 'controller/server/map/getjsonlisteq.php?fix=1', // сначала получаем список
							{orgid: defaultorgid, selpom: slpom},
							function (e) {
								zx = JSON.parse(e); // парсим JSON в массив
								PlaceTMC();
							});
				});

				// стильметки
				$('#stmetka').click(function () {
					if ($('#grpom').prop('checked')) {
						slpom = $('#splaces :selected').val();
					} else {
						slpom = 'null';
					}

					$.get(route + 'controller/server/map/getjsonlisteq.php?fix=1', // сначала получаем список
							{orgid: defaultorgid, selpom: slpom},
							function (e) {
								zx = JSON.parse(e); // парсим JSON в массив
								PlaceTMC();
							});
				});

				// выбираем помещение
				$('#splaces').click(function () {
					if ($('#grpom').prop('checked')) {
						slpom = $('#splaces :selected').val();
						$.get(route + 'controller/server/map/getjsonlisteq.php?fix=1', // сначала получаем список
								{orgid: defaultorgid, selpom: slpom},
								function (e) {
									zx = JSON.parse(e); // парсим JSON в массив
									PlaceTMC();
								});
					}

				});

				function getRandomCoordinates(maxLatitude, minLatitude, maxLongitude, minLongitude) {
					return [Math.random() * (maxLatitude - minLatitude) + minLatitude, Math.random() * (maxLongitude - minLongitude) + minLongitude];
				}
			}
}

// получаем изображение картинки по orgid. Если нету - возврат noimage.jpg
function GetMapByOrgId(orgid) {
	$.get(route + 'controller/server/map/getmapimagefilename.php?fix=1', {id: orgid}, function (e) {
		if (e != 'null') {
			imgmap = jQuery.trim(e);
			loadmap(imgmap);
		} else {
			imgmap = 'photos/noimage.jpg';
			loadmap(imgmap);
		}
	});
	GetListPlaces(defaultorgid, ''); // читаем список помещений
	GetListTmc($('#splaces :selected').val());
}

/////////////////////////////////
// Далее инициализация скрипта
/////////////////////////////////

var zx;

// Загружаем список помещений
GetListPlaces(defaultorgid, defaultuserid);
GetMapByOrgId(defaultorgid);
