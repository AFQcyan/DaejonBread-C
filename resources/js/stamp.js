
let file_handel

async function get_handel() {
    // 핸들을 얻는 함수입니다. 덤으로 가져온 파일의 정보도 반환합니다.
    // 참고로 showOpenFilePicker는 버튼을 클릭하는 등의 사용자의 입력을 받고 나서 실행해야합니다.
    // 저는 로드되자마자 실행시켜서 안되는 줄 알고 시간 날려먹었습니다.
    [file_handel] = await window.showOpenFilePicker()
    return await file_handel.getFile()
}
async function save(content, a) {
    // 얻은 핸들에 blob 형태의 내용을 써주고 저장합니다
    let stream = await file_handel.createWritable()
    stream.write(content)
    stream.close()

    // a가 참이면 룰렛을 돌립니다. a값을 넣지 않았거나 파일저장을 거부하면 이 코드가 실행되지 않습니다.
    if(a == true){
        spin()
    }
}
let code_list = []
let product_list = []

// 스탬프카드와 스탬프 이미지를 미리 불러와놓습니다.
const IMG = {
    CRAD: get_image("/resources/stamp/스탬프카드.png"),
    STAMP: get_image("/resources/stamp/스탬프.png")
}
const POINT = [
    {
        // x, y는 도형이나 사진을 그려야하는 위치, a,b는 검사할 픽셀의 위치, c는 스탬프 끼리의 거리 
        x: 20,
        y: 77,
        a: 123 - 20,
        b: 54 - 20,
        c: 125 - 77
    },
    {
        // x, y는 도형이나 사진을 그려야하는 위치, a,b는 검사할 픽셀의 위치, c는 스탬프 끼리의 거리
        x: 20,
        y: 173,
        a: 123 - 20,
        b: 54 - 20,
        c: 125 - 77
    },
    {
        // x, y는 도형이나 사진을 그려야하는 위치 및 검사할 픽셀의 위치 a는 스탬프 끼리의 거리
        x: 163,
        y: 271,
        a: 15
    }
]

function get_image(src) {
    let img = new Image()
    img.src = src
    return img
}

let spin_count = 0
function spin() {
    spin_count++
    let r = Math.floor(Math.random() * 9)

    // spin_count를 1씩 높여서 무조건 룰렛이 4바퀴 이상 돌게 해줍니다.
    document.querySelector('#roulette canvas').style.transform = `rotate(${r * 36 + spin_count * 360 * 4}deg)`

    setTimeout(() => {
        // 룰렛이 다 돌고나면 당첨결과를 알람창으로 띄워줍니다.
        alert(`축하합니다! ${product_list[r]}에 당첨 되었습니다.`)
    }, 1500);
}

function draw_roulette() {
    let colors = ['#0000fe', '#fe0000', '#008001', '#fe0000', '#0000fe', '#008001', '#0000fe', '#fe0000', '#008001', '#fe0000']
    let canvas = document.querySelector('canvas')
    let ctx = canvas.getContext('2d')

    // 10개의 상품이 있으므로 원의 둘레인 Math.PI*2를 10으로 나눠 1칸을 만들어줍니다. 
    let block = Math.PI * 2 / 10

    // 중앙정렬을 하지 않으면 엉망진창이 되므로 중앙정렬을 해줍니다.
    ctx.textAlign = 'center'

    for (let i = 0; i < 10; i++) {
        // 1칸을 중앙으로 이동 후 그립니다
        ctx.fillStyle = colors[i]
        ctx.beginPath()
        ctx.moveTo(250, 250)
        ctx.arc(250, 250, 250, block * -3, block * -2)
        ctx.fill()

        ctx.fillStyle = 'white'
        ctx.fillText(product_list[i], 250, 50)

        // 캔버스를 돌려서 다음 칸에도 그릴 수 있도록 합니다.
        ctx.translate(250, 250)
        ctx.rotate(block * -1)
        ctx.translate(-250, -250)
    }
}

function draw_arc(color, findcolor, ctx) {
    // findcolor를 찾은 후 color로 찾은 원을 대체합니다.
    for (let i = 0; i < 8; i++) {
        let data = ctx.getImageData(POINT[2].x + POINT[2].a * i, POINT[2].y, 1, 1).data
        console.log(data)
        if (data[0] == findcolor[0] && data[1] == findcolor[1] && data[2] == findcolor[2] && data[3] == findcolor[3]) {
            ctx.fillStyle = 'white'
            ctx.beginPath()
            ctx.arc(POINT[2].x + POINT[2].a * i, POINT[2].y, 4, 0, Math.PI * 2)
            ctx.fill()

            ctx.fillStyle = color
            ctx.beginPath()
            ctx.arc(POINT[2].x + POINT[2].a * i, POINT[2].y, 3, 0, Math.PI * 2)
            ctx.fill()

            return;
        }
    }
    return 'false'
}

function draw_name(name, ctx) {
    // 스탬프카드를 그린 후 하얀색으로 이름을 그립니다.
    ctx.fillStyle = 'white'
    ctx.drawImage(IMG.CRAD, 0, 0)
    ctx.fillText(name, 370, 20)
}

$(() => {
    // 상품 리스트와 쿠폰코드 리스트를 불러옵니다. draw_roulette이 실행되려면 룰렛 캔버스가 로딩 되어있어야 하므로 html 로딩이 끝나고 불러왔습니다.
    $.getJSON('/resources/stamp/code.json', json => {
        code_list = json
    })
    $.getJSON('/resources/stamp/product.json', json => {
        product_list = json
        draw_roulette()
    })
    
    // 이름을 입력하고 '발급하기' 버튼을 눌렀을때 실행됩니다.
    $('#down button').click(() => {
        // 이름을 입력하지 않았다면 발급해주지 않습니다.
        let name = $('#down input').val()
        if (name == '') {
            alert('이름을 입력해주세요')
            return
        }

        // 캔버스를 새로 만듭니다. 캔버스의 크기는 스탬프카드와 같아야하므로 스탬프카드의 width와 height를 이용합니다.
        let canvas = document.createElement('canvas')
        canvas.width = IMG.CRAD.width
        canvas.height = IMG.CRAD.height
        let ctx = canvas.getContext('2d')

        // 이름을 그립니다.
        draw_name(name, ctx)

        // 이름을 그린 스탬프카드를 다운로드 합니다.
        let a = document.createElement('a')

        a.href = canvas.toDataURL()
        a.download = "스탬프 카드.png"
        a.click()
    })

    // 스탬프 이벤트 참여 영역의 파일선택을 눌렀을때 실행됩니다.
    $('#roulette button').click(() => {
        stamp_scan()
    })

    // 쿠폰 코드 영역의 '스탬프 찍기'를 클릭할시 실행됩니다.
    $('#code button').click(() => {
        // 입력한 코드가 옳은 코드라면 모달팝업을 띄웁니다.
        let code = $('#code input').val()
        if (code_list.some(x => x == code)) {
            window.location = '#file'
        }
    })

    // 쿠폰 코드 영역의 모달팝업에서 파일 선택을 클릭할시 실행됩니다.
    $('#file button').click(() => {
        stamp_draw()
    })
})

// 스탬프 카드의 사용가능 횟수를 늘려주고 스탬프를 찍어줍니다.
async function stamp_draw() {
    let data = await get_handel()
    let reader = new FileReader()

    reader.addEventListener('load', () => {
        let base64 = reader.result

        // 불러온 base64를 이미지로 변환합니다.
        let img = new Image()
        img.src = base64


        // 새롭게 그릴 캔버스를 준비합니다.
        let canvas = document.createElement('canvas')
        canvas.width = IMG.CRAD.width
        canvas.height = IMG.CRAD.height
        let ctx = canvas.getContext('2d')

        // 불러온 이미지가 로드됐다면 실행됩니다.
        img.addEventListener('load', () => {
            // 불러온 스탬프 카드를 캔버스에 그려줍니다.
            ctx.drawImage(img, 0, 0)
            let is_break = false;

            // getImageData로 특정 픽셀이 특정 색깔인지 검사해서 스탬프가 찍혀있는지 확인하고 찍히지 않은 스탬프를 발견하면 그 자리에 스탬프를 찍습니다.
            for (let i = 0; i < 4; i++) {
                let data = ctx.getImageData(POINT[0].x + POINT[0].b + POINT[0].a * i, POINT[0].y + POINT[0].c, 1, 1).data
                console.log(data)
                if (data[0] == 176 && data[1] == 179 && data[2] == 180 && data[3] == 255) {
                    ctx.fillStyle = 'white'
                    ctx.fillRect(POINT[0].x+ POINT[0].a * i, POINT[0].y, 90, 90)
                    ctx.drawImage(IMG.STAMP, POINT[0].x + POINT[0].a * i, POINT[0].y)
                    is_break = true
                    break;
                }
            }
            if (!is_break) {
                for (let i = 0; i < 4; i++) {
                    let data = ctx.getImageData(POINT[1].x + POINT[1].b + POINT[1].a * i, POINT[1].y + POINT[1].c, 1, 1).data
                    if (data[0] == 176 && data[1] == 179 && data[2] == 180 && data[3] == 255) {
                        ctx.fillStyle = 'white'
                        ctx.fillRect(POINT[1].x, POINT[1].y, 90, 90)
                        ctx.drawImage(IMG.STAMP, POINT[1].x + POINT[0].a * i, POINT[1].y)
                        break;
                    }
                }
            }

            // [135, 137, 139, 255](비어있는 색)을 찾아서 초록색으로 변경합니다.
            draw_arc('rgb(20,240,20)', [135, 137, 139, 255], ctx)

            // 다 그렸다면 이미지를 저장합니다.
            canvas.toBlob(b => {
                save(b)
                window.location = '#'
            })
        })
    })

    // 선택한 파일 내용을 base64로 변환시킵니다.
    reader.readAsDataURL(data)
}

// 스탬프 카드의 사용가능 횟수가 남아있는지 검사하고 사용횟수를 차감시키고 룰렛을 돌립니다.
async function stamp_scan() {
    let data = await get_handel()
    let reader = new FileReader()

    reader.addEventListener('load', () => {
        let base64 = reader.result

        // 불러온 base64를 이미지로 변환합니다.
        let img = new Image()
        img.src = base64

        // 새롭게 그릴 캔버스를 준비합니다.
        let canvas = document.createElement('canvas')
        canvas.width = IMG.CRAD.width
        canvas.height = IMG.CRAD.height
        let ctx = canvas.getContext('2d')

        // 불러온 이미지가 로드됐다면 실행됩니다.
        img.addEventListener('load', () => {
            // 불러온 스탬프 카드를 캔버스에 그려줍니다.
            ctx.drawImage(img, 0, 0)

            // 사용횟수에서 색 [20, 240, 20, 255](초록)을 찾을 수 없다면 이벤트에 더 이상 참여할 수 없으니 save와 spin을 실행시키지 않습니다.
            if(draw_arc('rgb(240,20,20)', [20, 240, 20, 255], ctx) == 'false'){
                alert("이벤트에 더 이상 참여 할 수 없다")
            } else{
                // 사용횟수가 남아있다면 사용횟수가 1개 줄어든 다음 그려져 있는 스탬프카드를 저장합니다.
                canvas.toBlob(b => {
                    save(b, true)
                    window.location = '#'
                })
            }
        })
    })

    // 선택한 파일 내용을 base64로 변환시킵니다.
    reader.readAsDataURL(data)
}