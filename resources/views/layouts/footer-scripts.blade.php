
<script src="{{asset('assets/js/vendor.min.js')}}"></script>

<!-- Plugins js-->
<script src="{{asset('assets/libs/jquery-sparkline/jquery.sparkline.min.js')}}"></script>
<script src="{{asset('assets/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.min.js')}}"></script>
<script src="{{asset('assets/libs/admin-resources/jquery.vectormap/maps/jquery-jvectormap-world-mill-en.js')}}"></script>
<script src="{{asset('assets/js/vendor.min.js')}}"></script>
<script src="{{asset('assets/libs/apexcharts/apexcharts.min.js')}}"></script>

<!-- Plugins js-->
<script src="{{asset('assets/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.min.js')}}"></script>
<script src="{{asset('assets/admin-resources/jquery.vectormap/maps/jquery-jvectormap-world-mill-en.js')}}"></script>
<!-- Sweet Alerts js -->
<script src=" {{ asset('assets/js/pages/sweetalert2.all.min.js') }} "></script>
<script src=" {{ asset('assets/js/pages/sweetalert.bundle.js') }} "></script>
<script src=" {{ asset('assets/js/pages/ui-sweetalert.min.js') }} "></script>
<script src=" {{ asset('assets/js/pages/sweet-alerts.init.js') }} "></script>

<!-- Dashboard init js -->
<script src="{{asset('assets/js/pages/ecommerce-dashboard.init.js')}}"></script>




<!-- Dashboard 2 init -->
<script src="{{asset('assets/js/pages/dashboard-2.init.js')}}"></script>

<!-- App js-->
<script src="{{asset('assets/js/app.min.js')}}"></script>

@yield('scripts')
@yield('sripts')
<script>
document.querySelector('.sidebar-toggle').addEventListener('click', function() {
    document.body.classList.toggle('sidebar-closed');
});
</script>
<script src=" {{ asset('assets/js/pages/html2pdf.bundle.min.js')}}"></script>


@livewireScripts
<script>
    function downloadPDF() {
        const element = document.body; // Replace with the specific element you want to download
        const options = {
            margin:       0.5,
            filename:     'download.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
        };

        html2pdf().set(options).from(element).save();
    }

</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>



<script>
    function removeImage() {
        const logo = document.getElementById("logo");
        if (logo) {
            logo.style.display = "none";
        }
    }


        function JSconfirm(event, companyId) {
            event.preventDefault(); // Prevent immediate form submission

            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this record!",
                icon: "warning",
                buttons: ["Cancel", "Yes, delete it!"],
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    // Submit the form if confirmed
                    document.getElementById('deleteform' + companyId).submit();
                }
            });
        }

        function JSconfirmCreditNote(event, id) {
            event.preventDefault();

            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this credit note!",
                icon: "warning",
                buttons: ["Cancel", "Yes, delete it!"],
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    document.getElementById('deletecreditnote' + id).submit();
                }
            });
        }

</script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
