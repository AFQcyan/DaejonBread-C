// 현재 이동된 좌표와 현재 지도 크기 단계입니다.
let current_point = [0, 0]
let current_size = 0

// 맵을 그려놓은 캔버스를 담아둡니다.
// 화면에 빵집 주소를 표시해야하므로 지도보기 버튼을 누를때 마다 다시 준비됩니다.
let MAP = []

// 화면에 출력되어 있는 캔버스 입니다.
let map_canvas
let map_ctx

// 각 단계별 맵 크기 입니다.
const SIZE = [800, 1600, 3200]

// 이미지를 모두 불러옵니다.
const IMG = [
    [
        get_image("resources/map/1/1.jpg")
    ],
    [
        get_image("resources/map/2/2-1.jpg"),
        get_image("resources/map/2/2-2.jpg"),
        get_image("resources/map/2/2-3.jpg"),
        get_image("resources/map/2/2-4.jpg"),
    ],
    [
        get_image("resources/map/3/3-1.jpg"),
        get_image("resources/map/3/3-2.jpg"),
        get_image("resources/map/3/3-3.jpg"),
        get_image("resources/map/3/3-4.jpg"),
        get_image("resources/map/3/3-5.jpg"),
        get_image("resources/map/3/3-6.jpg"),
        get_image("resources/map/3/3-7.jpg"),
        get_image("resources/map/3/3-8.jpg"),
        get_image("resources/map/3/3-9.jpg"),
        get_image("resources/map/3/3-10.jpg"),
        get_image("resources/map/3/3-11.jpg"),
        get_image("resources/map/3/3-12.jpg"),
        get_image("resources/map/3/3-13.jpg"),
        get_image("resources/map/3/3-14.jpg"),
        get_image("resources/map/3/3-15.jpg"),
        get_image("resources/map/3/3-16.jpg")
    ],
    get_image('resources/map/marker.png')
]

// 빵집들의 위치입니다.
let store_point = []

$.getJSON('resources/map/store_location.json', json => {
    // 빵집들의 위치를 불러왔으면 MAP을 설정해줍니다.
    store_point = json
    MAP = [
        set_map(0, 1, 1),
        set_map(1, 2, 1),
        set_map(2, 4, 1)
    ]
})

function set_map(img, size, id) {
    // img는 IMG 상수배열에서 이미지를 가져올떄 사용하고
    // size는 캔버스 사이즈를 그리거나 반복문을 실행할떄 사용합니다.
    // id는 store_point에 들어있는 빵집의 좌표를 얻기 위해 사용합니다.
    let canvas = document.createElement('canvas')
    let ctx = canvas.getContext('2d')
    canvas.width = size * 800
    canvas.height = size * 800

    // 지도를 크기에 맞게 그립니다.
    for (let i = 0; i < size; i++) {
        for (let j = 0; j < size; j++) {
            ctx.drawImage(IMG[img][i * size + j], 800 * j, 800 * i, 800 + size, 800 + size)
        }
    }

    // 지도에 빵집을 표시합니다. 좌표 / 기준크기(4400) * size * 800을 하면 됩니다. 가로는 가로 크기의 반을 빼줘야합니다
    ctx.drawImage(IMG[3], store_point[id].x / 4400 * size * 800 - 30, store_point[id].y / 4400 * size * 800, 40, 65)

    return canvas
}

let mousedown = false
let is_zooming = false

$(() => {
    map_canvas = document.querySelector('#map canvas')
    map_ctx = map_canvas.getContext('2d')

    // 마우스를 눌렀을떄 mousedown이 true가 되고 마우스를 떄거나 캔버스에서 마우스가 나간경우
    // mousedown이 false가 됩니다. 그리고 mousedown이 true일떄 마우스를 움직이면 move함수(지도를 이동시키는 함수)가 실행됩니다.
    map_canvas.addEventListener('mousemove', e => {
        if (!mousedown) {
            return
        }

        move(e.movementX, e.movementY)
    })
    map_canvas.addEventListener('mousedown', e => {
        mousedown = true
    })
    map_canvas.addEventListener('mouseup', e => {
        mousedown = false
    })
    map_canvas.addEventListener('mouseleave', e => {
        mousedown = false
    })

    // 마우스의 휠을 돌렸고 현재 줌인 또는 줌 아웃이 실행되고 있지 않다면 zoom함수를 실행시킵니다.
    // (줌인 또는 줌 아웃을 합니다.)
    map_canvas.addEventListener('mousewheel', e => {
        if (is_zooming) {
            return
        }
        is_zooming = true
        zoom(e.deltaY, e.offsetX, e.offsetY)
    })
    map_canvas.addEventListener('DONMouseScroll', e => {
        if (is_zooming) {
            return
        }
        is_zooming = true
        zoom(e.deltaY, e.offsetX, e.offsetY)
    })

    //지도보기 버튼을 클릭할시 MAP 변수에 들어가 있는 캔버스들을 다시 그려줍니다.
    document.querySelectorAll('a[href="#map"]').forEach(x => x.addEventListener('click', e => {
        current_point = [0, 0]
        current_size = 0
        is_zooming = false
        MAP = [
            set_map(0, 1, e.target.dataset.id),
            set_map(1, 2, e.target.dataset.id),
            set_map(2, 4, e.target.dataset.id)
        ]
        map_ctx.drawImage(MAP[0], 0, 0)
    }))

    // 확인용 입니다.
    setTimeout(() => {
        map_ctx.drawImage(MAP[0], 0, 0)
    }, 500)
})

function zoom(scroll, x, y) {
    // x와 y는 줌을 할떄 캔버스를 기준으로 한 마우스 위치입니다.

    // current_point에 줌을 할때 마우스 위치를 뺸 값에 현재 지도 크기를 나눔으로써
    // 이동해야하는 거리를 비율로 구할 수 있습니다.
    const norX = (current_point[0] - x) / SIZE[current_size];
    const norY = (current_point[1] - y) / SIZE[current_size];

    // Event.deltaY가 양수라면 휠을 아래로 내린것이므로 확대, 
    // 음수일시 휠을 위로 올린 것이므로 축소를 합니다
    if (scroll > 0) {
        if (current_size >= 2) {
            is_zooming = false
            return
        }

        // 구한 비율에 줌이 끝나고 나서 될 크기를 곱해줌으로써 이동해야하는 좌표를 구하고
        // 화면크기의 반인 400을 더해줘서 한 쪽으로 쏠리는 걸 방지합니다
        // 그리고 현재 좌표를 빼서 이동해야하는 거리를 구합니다 (이동해야하는 거리에 반복횟수만큼 나눠서 천천히 확대되면서 이동되게 해야합니다.)
        const newX = norX * SIZE[current_size + 1] + 400 - current_point[0];
        const newY = norY * SIZE[current_size + 1] + 400 - current_point[1];
        console.log(newY)
        zoomin(0, [newX, newY])
    } else {
        if (current_size <= 0) {
            is_zooming = false
            return
        }
        const newX = norX * SIZE[current_size - 1] + 400 - current_point[0];
        const newY = norY * SIZE[current_size - 1] + 400 - current_point[1];
        zoomout(0, [newX, newY])
    }
}


function zoomin(i, new_point) {
    if (i > 50) {
        // 현재 크기 변수에 1을 더하고 그에 맞는 좌표, 지도 크기등으로 지도를 새로 그리고 함수를 종료합니다.
        current_size++
        current_point = point_scan(current_point, SIZE[current_size])
        map_ctx.drawImage(MAP[current_size], current_point[0], current_point[1])
        is_zooming = false
        return
    }

    // 지도 크기가 줌 인을 할때마다 2배가 되야하므로 현재크기 + 현재크기를 해야하지만
    // 줌 인 효과를 주려면 나눠서 천천히 확대돼야하므로 반복횟수를 나눠주고 현재 반복한 횟수를 곱해준다.
    let size = SIZE[current_size] + (SIZE[current_size] / 50 * i)

    // 이 함수를 실행시키기 전에 zoom함수에서 구했던 이동해야할 거리에 반복횟수를 나누고 더해서 천천히 목표 좌표로 이동하게 합니다. 
    current_point = [current_point[0] + new_point[0] / 50, current_point[1] + new_point[1] / 50]
    // 혹시나 줌인을 하는 도중 지도가 화면 밖으로 벗어 날 수 있으므로 현재좌표가 옳은 좌표인지 검사합니다.
    current_point = point_scan(current_point, size)

    // 방금 구했던 좌표, 크기등을 이용해서 캔버스에 지도를 그립니다.
    map_ctx.drawImage(MAP[current_size], current_point[0], current_point[1], size, size)

    // 10ms 후에 이 함수를 또 실행시키므로써 천천히 확대되게 합니다.
    setTimeout(zoomin, 10, i + 1, new_point)
}

function zoomout(i, new_point) {
    if (i > 50) {
        current_size--
        current_point = point_scan(current_point, SIZE[current_size])
        map_ctx.drawImage(MAP[current_size], current_point[0], current_point[1])
        is_zooming = false
        return
    }
    // 지도 크기가 줌 인을 할때마다 1/2배가 되야하므로 현재크기 - 현재크기/2를 해야하지만
    // 줌 아웃 효과를 주려면 나눠서 천천히 축소돼야하므로 반복횟수를 나눠주고 현재 반복한 횟수를 곱해준다.
    let size = SIZE[current_size] - SIZE[current_size] / 50 * i / 2

    current_point = [current_point[0] + new_point[0] / 50, current_point[1] + new_point[1] / 50]
    current_point = point_scan(current_point, size)

    map_ctx.drawImage(MAP[current_size], current_point[0], current_point[1], size, size)

    setTimeout(zoomout, 10, i + 1, new_point)
}


function move(x, y) {
    // 현재 좌표에 이동거리를 더해서 위치를 바꿉니다. 이동하는 도중 지도의 끝이 보이게 될 수 있기 떄문에 현재 좌표를 검사하여 빠져나갔는지 확인해줍니다.
    current_point[0] += x
    current_point[1] += y
    current_point = point_scan(current_point, SIZE[current_size])
    map_ctx.drawImage(MAP[current_size], current_point[0], current_point[1])
}


function point_scan(point, size) {
    let [x, y] = point

    // 좌표가 x보다 크면 왼쪽에 여백이 생기고
    // 지도 크기 - 캔버스 크기보다 작으면 오른쪽에 여백이 생기므로 막아줘야합니다.
    if (x > 0) {
        x = 0
    } else if (x < (size - 800) * -1) {
        x = (size - 800) * -1
    }

    if (y > 0) {
        y = 0
    } else if (y < (size - 800) * -1) {
        y = (size - 800) * -1
    }

    return [x, y]
}


function get_image(src) {
    // src받고 이미지를 리턴하는 간단한 함수입니다.
    let img = new Image()
    img.src = src
    return img
}