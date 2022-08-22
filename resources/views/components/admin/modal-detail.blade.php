

<!-- detail modal -->
<div id="modal-detail" tabindex="-1" class="bg-[rgba(100,116,139,0.3)] overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full inset-0 h-modal h-full justify-center items-center flex transition-all opacity-0 hidden" aria-modal="true" role="dialog">
    <div class="relative p-4 w-full max-w-2xl h-auto max-h-[75vh] overflow-y-auto">
        <!-- Modal content -->
        <div class="modal-detail-content relative overflow-y-auto bg-white rounded-lg shadow">
            <!-- Modal header dan body -->
            {{ $slot }}
            <!-- Modal footer -->
            <div class="flex items-center p-6 mx-5 space-x-2 rounded-b border-t border-gray-200">
                <button data-modal-toggle="defaultModal" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-md text-sm px-5 py-2.5 text-center ">Save</button>
                <button data-modal-toggle="defaultModal" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-md border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 close-modal-detail">Close</button>
            </div>
        </div>
    </div>
</div>

@push('add-script')
<script>
    let openModal = document.querySelectorAll('.btn-detail');
    let modalDetail = document.querySelector('#modal-detail');
    let modalClose = document.querySelector('.close-modal-detail');
    let modalHeader = modalDetail.querySelectorAll('.modal-detail-content > div')[0];
    let modalBody = modalDetail.querySelectorAll('.modal-detail-content > div')[1];
    let modalData;

    let modalBodySTR = modalBody.innerHTML;
    let modalHeaderSTR = modalHeader.innerHTML;

    //------------ function untuk toggle modal -----------
    function toggleModal(){
        if(modalDetail.classList.contains('hidden')){
            modalDetail.classList.remove('hidden');
            setTimeout(() => {
                modalDetail.classList.remove('opacity-0');
            }, 10);
        }else{
            modalDetail.classList.add('opacity-0');
            setTimeout(() => {
                modalDetail.classList.add('hidden');
            }, 350);
        }
    }

    //------------ menutup modal ----------------
    modalClose.addEventListener('click', ()=> toggleModal());
    modalDetail.addEventListener('click',(e)=> e.target == modalDetail? toggleModal() : false);

    // ----------- membuka modal ---------------
    //disini tempat tag template di proses untuk diubah menjadi data
    //untuk ketentuanya yaitu
    //<fill_{nama data}></fill_{nama data}>
    function updateDetailModal(data, model){
        let modalBodyTempSTR = modalBodySTR
        let modalHeaderTempSTR = modalHeaderSTR
        let dat;
        if(data != undefined){
            console.log(model)
            for(let key in data){
                console.log(key, data[key])
                if(key == 'source' || key == 'image'){
                    modalBodyTempSTR = sourceInsert(data, modalBodyTempSTR, model);
                    continue;
                }
                if(key == 'author'){
                    console.log('insert untuk author')
                    userInsert(data.author) //akan melakukan insert data dengan menggunakan manipulasi DOM
                }
                if(key == 'created_at'){
                    modalBodyTempSTR = modalBodyTempSTR.replace((new RegExp(`<fill_${key}>(.*?)<\/fill_${key}>`, 'g')),data[key].split('T')[0]) || ''; //regular expression untuk mendapatkan data dari elemn html
                    modalHeaderTempSTR = modalHeaderTempSTR.replace((new RegExp(`<fill_${key}>(.*?)<\/fill_${key}>`, 'g')),data[key].split('T')[0]) || ''; //regular expression untuk mendapatkan data dari elemn html
                    continue;
                }
                if(key == 'type'){
                    modalBodyTempSTR = typeInsert(data['type'], modalBodyTempSTR);
                }
                if(key == 'category'){
                    modalBodyTempSTR = categoryInsert(data['category'], modalBodyTempSTR)
                }
                modalBodyTempSTR = modalBodyTempSTR.replace((new RegExp(`<fill_${key}>(.*?)<\/fill_${key}>`, 'g')),data[key]) || '';
                modalHeaderTempSTR = modalHeaderTempSTR.replace((new RegExp(`<fill_${key}>(.*?)<\/fill_${key}>`, 'g')),data[key]) || '';
                // console.log(modalBodySTR)
                // modalBody = modalBodySTR.replace(new RegExp(`<fill_${key}>(.*?)<\/fill_${key}>`),data[key]) 
            }
        }else{
            modalBodyTempSTR = `
            <div class="bg-red-100 max-w-full mx-auto rounded-md p-4">
                <p class="font-bold text-red-800">Terjadi Kesalaahan</p>
                <p class="text-red-800">mungkin disebabkan hal-hal berikut :</p>
                <hr class="bg-red-600 my-2">
                <ul class="list-disc list-inside text-red-600">
                    <li>Jaringan Internet</li>
                    <li>Proxy atau VPN</li>
                    <li>Server yang bermasalah</li>
                    <li>Data tidak ada / valid</li>
                </ul>
            </div>
            `;
            modalHeaderTempSTR = `
            <h3 class="text-xl font-semibold text-gray-900">
                Data bermasalah
            </h3>
            `;
        }
        
        // console.log(modalBodySTR);
        modalHeader.innerHTML = modalHeaderTempSTR;
        modalBody.innerHTML = modalBodyTempSTR;
    }

    function userInsert(user){
        let table = document.querySelector('#table-user')
        table.querySelector('#name-show').innerHTML = user.name
        table.querySelector('#username-show').innerHTML = user.username
        table.querySelector('#email-show').innerHTML = user.email
        console.log(table)
        console.log(table.parent)
        // return table.parent.parent.outerHTML
    }

    //khusun untuk data dengan tipe source maka harus ada pengecekan tertentu
    function sourceInsert(data, modalBodyTempSTR, modelPath){
        //bila ada colum type
        let str = ``;
        let resReg = '';
        switch(modelPath){
            case 'galeri':
                if(Object.keys(data).find(e => e == 'type')){
                    if(data.type == 'image'){
                        str = `
                        <img src='/storage/${data.source}' />
                        `
                        resReg =  modalBodyTempSTR.replace((new RegExp(`<fill_source>(.*?)<\/fill_source>`, 'g')),str) || '';
                        return resReg.replace((new RegExp(`<fill_source_text>(.*?)<\/fill_source_text>`, 'g')),data.source) || resReg; //diproses kembali supaya hasil yg diharapkan berupa text dapat ditampilkan
                    }else if(data.type == 'video'){
                        resReg =  modalBodyTempSTR.replace((new RegExp(`<fill_source>(.*?)<\/fill_source>`, 'g')),data.source) || '';
                        return resReg;
                    }
                    console.error('tidak ada tipe yang sesuai dengan source')
                    return modalBodyTempSTR
                }
            break
            case 'berita' :
                str = `
                    <img src='/storage/${data.image}' />
                    `
                    resReg =  modalBodyTempSTR.replace((new RegExp(`<fill_image>(.*?)<\/fill_image>`, 'g')),str) || '';
                    return resReg.replace((new RegExp(`<fill_image_text>(.*?)<\/fill_image_text>`, 'g')),data.image) || resReg; //diproses kembali supaya hasil yg diharapkan berupa text dapat ditampilkan
            break
        }

        console.error('kolom tipe tidak ada')
        return modalBodyTempSTR;

    }



    function typeInsert(type, modalBodyTempSTR){
        let str = ``;
        switch(type){
            case 'image':
                str = `
                <p class="text-base  leading-relaxed bg-[#facc15] rounded-full px-3 mx-2">
                    Image
                </p>
                `
                return modalBodyTempSTR.replace((new RegExp(`<fill_type>(.*?)<\/fill_type>`, 'g')),str) || '';
                break
            case 'video':
                str = `
                <p class="text-base  leading-relaxed bg-[#71FF40] rounded-full px-3 mx-2">
                    Video
                </p>
                `
                return modalBodyTempSTR.replace((new RegExp(`<fill_type>(.*?)<\/fill_type>`, 'g')),str) || '';
                break
            default:
                console.error('ttipe tidak valid')
                return modalBodyTempSTR;
        }
    }
    function categoryInsert(category, modalBodyTempSTR){
        console.log('tipe adalah', category)
        let str = ``;
        switch(category){
            case 'berita':
                str = `
                <p class="text-base  leading-relaxed bg-[#facc15] rounded-full px-3 mx-2">Berita</p>
                `
                return modalBodyTempSTR.replace((new RegExp(`<fill_category>(.*?)<\/fill_category>`, 'g')),str) || '';
                break
            case 'goverment':
                str = `
                <p class="text-base  leading-relaxed bg-[#71FF40] rounded-full px-3 mx-2">Goverment</p>
                `
                return modalBodyTempSTR.replace((new RegExp(`<fill_category>(.*?)<\/fill_category>`, 'g')),str) || '';
                break
            case 'technology':
            str = `
                <p class="text-base  leading-relaxed bg-[#0091ff] rounded-full px-3 mx-2">Technology</p>
                `
                return modalBodyTempSTR.replace((new RegExp(`<fill_category>(.*?)<\/fill_category>`, 'g')),str) || '';
                break
            default:
                console.error('ttipe tidak valid')
                return modalBodyTempSTR;
        }
    }

    function getData (id){
        return fetch(`/admin/{{ $modelPath }}/${id}`)
            .then(res => res.json())
            .then(res => res)
            .catch(err => err)
    }

    openModal.forEach( btn => {
        btn.addEventListener('click', async function(e){
            try{
                modalData = await getData(this.getAttribute('data-id'));
                console.log(modalData);
                console.log(this.getAttribute('data-id'));
                updateDetailModal(modalData[0],"{{ $modelPath }}");
                toggleModal();
            }catch(err){
                console.error(err);
            }
        });
    })
</script>
@endpush
