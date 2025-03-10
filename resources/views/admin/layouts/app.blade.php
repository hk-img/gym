<!DOCTYPE html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none">


<!-- Mirrored from smarthr.dreamstechnologies.com/laravel/template/public/admin-dashboard by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 12 Nov 2024 05:34:38 GMT -->
<!-- Added by HTTrack -->
<meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Smarthr - Bootstrap Admin Template">
    <meta name="keywords"
        content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, accounts, invoice, html5, responsive, CRM, Projects">
    <meta name="author" content="Dreamstechnologies - Bootstrap Admin Template">
    <title>@yield('page_title') - {{env('PAGE_TITLE', 'Admin Panel')}}</title>
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('assets/img/favicon.png')}}">

    <!-- custom css -->
    <link rel="stylesheet" href="{{ asset('assets/css/customstyle.css') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">


    @include('admin.layouts.partials.style')

    @stack('custom-style')
</head>

<body class="{{ Route::is(['admin.login','admin.password.request']) ? 'account-page' : ''}}">
    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <div id="loader-wrapper">
            <div id="loader">
              <div class="loader-ellips">
                <span class="loader-ellips__dot"></span>
                <span class="loader-ellips__dot"></span>
                <span class="loader-ellips__dot"></span>
                <span class="loader-ellips__dot"></span>
              </div>
            </div>
          </div>    
        @auth
        <!-- Header -->
        @include('admin.layouts.partials.header')
        <!-- /Header -->

        <!-- Sidebar -->
        @include('admin.layouts.partials.sidebar')
        <!-- /Sidebar -->
        @endauth




        @auth
        <!-- Two Col Sidebar -->
        @include('admin.layouts.partials.two_col_sidebar')
        <!-- /Two Col Sidebar -->
        @endauth

        <!-- Page Wrapper -->
        @yield('content')
        <!-- /Page Wrapper -->

    </div>
    <!-- /Main Wrapper -->
    </div>
    </div>
    <!-- /Page Wrapper -->

    </div>
    <!-- /Main Wrapper -->
    @auth
    {{-- @include('admin.layouts.partials.settings') --}}
    @endauth

    @include('admin.layouts.partials.script')
    @stack('custom-script')

    <script>
        // Confirm Delete Function
        function confirmDelete(formId) {
            console.log(formId);
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }

        // Confirm Status Change Function
        function confirmChangeStatus(formId) {
            console.log(formId);
            Swal.fire({
                title: "Are you sure?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, I'm sure!"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }

        // Success Message on Success
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: "{{ session('success') }}",
            });
        @endif
        
        // Error Message on Error
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: "{{ session('error') }}",
            });
        @endif
    </script>

    <script>
        function initializeSelect2(selector, url, placeholder = 'Select an option', extraData = {}) {
            $(selector).select2({
                ajax: {
                    url: url,
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        let requestData = { term: params.term };

                        // Only include extraData if it's not empty
                        if (Object.keys(extraData).length > 0) {
                            Object.assign(requestData, extraData);
                        }

                        return requestData;
                    },
                    processResults: function(data) {
                        var isAttributeList = $(selector).hasClass('attributeList');
                        var formattedData = data.map(function(item) {
                            if(selector == '.userList'){
                                var name = `${item.name} ${item.phone ? '- ('+ '+91 '+ item.phone +')' : ''} ${item.email ? '- ('+ item.email +')' : ''}`
                            }else{
                                var name = item.name
                            }
                            return { id: item.id, text: name,  data: item, filter: isAttributeList ? item.attribute_type : null  };
                        });
                        return { results: formattedData };
                    },
                    cache: true
                },
                allowClear: true,
                placeholder: placeholder,
                escapeMarkup: function(markup) {
                    return markup;
                }
            });

            // Conditionally set 'multiple' and 'tags' options
            if ($(selector).is('.fuelTypeList, .transmissionTypeList')) {
                $(selector).select2('destroy').select2({
                    ajax: {
                        url: url,
                        dataType: 'json',
                        delay: 250,
                        processResults: function(data) {
                            var formattedData = data.map(function(item) {
                                var name;
                                if ($(selector).hasClass('userList')) {
                                    name = `${item.name} ${item.phone ? '- ('+ item.phone +')' : ''} ${item.email ? '- ('+ item.email +')' : ''}`;
                                } else {
                                    name = item.name;
                                }
                                return { id: item.id, text: name };
                            });
                            return { results: formattedData };
                        },
                        cache: true
                    },
                    multiple: true,
                    allowClear: true,
                    placeholder: placeholder,
                    escapeMarkup: function(markup) {
                        return markup;
                    }
                });
            }

            if ($(selector).is('.tagList')) {
                $(selector).select2('destroy').select2({
                    ajax: {
                        url: url,
                        dataType: 'json',
                        delay: 250,
                        processResults: function(data) {
                            var formattedData = data.map(function(item) {
                                var name;
                                if ($(selector).hasClass('userList')) {
                                    name = `${item.name} ${item.phone ? '- ('+ item.phone +')' : ''} ${item.email ? '- ('+ item.email +')' : ''}`;
                                } else {
                                    name = item.name;
                                }
                                return { id: item.id, text: name };
                            });
                            return { results: formattedData };
                        },
                        cache: true
                    },
                    multiple: true,
                    tags: true,
                    allowClear: true,
                    placeholder: placeholder,
                    escapeMarkup: function(markup) {
                        return markup;
                    }
                });
            }
        }

        function initializeDataTable(ajaxUrl,filterSelectors, columnDefinitions) {
            const table = $('.datatable').DataTable({
                // "bFilter": false
                "language": {
                    paginate: {
                        next: ' <i class=" fa fa-angle-double-right"></i>',
                        previous: '<i class="fa fa-angle-double-left"></i> '
                    },
                },
                processing: true,
                serverSide: true,
                ajax: {
                        url: ajaxUrl,
                        data: function (d) {
                            // Dynamically append filter values based on selectors
                            filterSelectors.forEach(selector => {
                                d[selector.name] = $(selector.selector).val();
                            });
                        },
                    },
                columns: columnDefinitions,
            });

            // Attach click event to the search button
            $('.btn-search').on('click', function () {
                table.ajax.reload();
            });

            // Attach click event to the clear filter button
            $('.btn-clear').on('click', function () {
                // Reset all filters
                filterSelectors.forEach(selector => {
                    if ($(selector.selector).is('select')) {
                        $(selector.selector).val('').trigger('change');
                    } else {
                        $(selector.selector).val('');
                    }
                });

                // Reload the DataTable
                table.ajax.reload();
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const editorElement = document.querySelector('#ckeditor');
            if (editorElement) {
                const {
                    ClassicEditor,
                    Alignment,
                    AutoLink,
                    Autosave,
                    BalloonToolbar,
                    BlockQuote,
                    Bold,
                    Bookmark,
                    Code,
                    CodeBlock,
                    Essentials,
                    FontBackgroundColor,
                    FontColor,
                    FontFamily,
                    FontSize,
                    GeneralHtmlSupport,
                    Heading,
                    Highlight,
                    HorizontalLine,
                    HtmlEmbed,
                    Indent,
                    IndentBlock,
                    Italic,
                    Link,
                    Mention,
                    Paragraph,
                    PasteFromOffice,
                    RemoveFormat,
                    SpecialCharacters,
                    Strikethrough,
                    Style,
                    Subscript,
                    Superscript,
                    Table,
                    TableCellProperties,
                    TableProperties,
                    TableToolbar,
                    Underline
                } = window.CKEDITOR;

                /**
                * This is a 24-hour evaluation key. Create a free account to use CDN: https://portal.ckeditor.com/checkout?plan=free
                */
                const LICENSE_KEY = "{{env('CKEDITOR')}}";

                const editorConfig = {
                    toolbar: {
                        items: [
                            'heading',
                            'style',
                            '|',
                            'fontSize',
                            'fontFamily',
                            'fontColor',
                            'fontBackgroundColor',
                            '|',
                            'bold',
                            'italic',
                            'underline',
                            '|',
                            'link',
                            'insertTable',
                            'highlight',
                            'blockQuote',
                            'codeBlock',
                            '|',
                            'alignment',
                            '|',
                            'outdent',
                            'indent'
                        ],
                        shouldNotGroupWhenFull: false
                    },
                    plugins: [
                        Alignment,
                        AutoLink,
                        Autosave,
                        BalloonToolbar,
                        BlockQuote,
                        Bold,
                        Bookmark,
                        Code,
                        CodeBlock,
                        Essentials,
                        FontBackgroundColor,
                        FontColor,
                        FontFamily,
                        FontSize,
                        GeneralHtmlSupport,
                        Heading,
                        Highlight,
                        HorizontalLine,
                        HtmlEmbed,
                        Indent,
                        IndentBlock,
                        Italic,
                        Link,
                        Mention,
                        Paragraph,
                        PasteFromOffice,
                        RemoveFormat,
                        SpecialCharacters,
                        Strikethrough,
                        Style,
                        Subscript,
                        Superscript,
                        Table,
                        TableCellProperties,
                        TableProperties,
                        TableToolbar,
                        Underline
                    ],
                    balloonToolbar: ['bold', 'italic', '|', 'link'],
                    fontFamily: {
                        supportAllValues: true
                    },
                    fontSize: {
                        options: [10, 12, 14, 'default', 18, 20, 22],
                        supportAllValues: true
                    },
                    heading: {
                        options: [
                            {
                                model: 'paragraph',
                                title: 'Paragraph',
                                class: 'ck-heading_paragraph'
                            },
                            {
                                model: 'heading1',
                                view: 'h1',
                                title: 'Heading 1',
                                class: 'ck-heading_heading1'
                            },
                            {
                                model: 'heading2',
                                view: 'h2',
                                title: 'Heading 2',
                                class: 'ck-heading_heading2'
                            },
                            {
                                model: 'heading3',
                                view: 'h3',
                                title: 'Heading 3',
                                class: 'ck-heading_heading3'
                            },
                            {
                                model: 'heading4',
                                view: 'h4',
                                title: 'Heading 4',
                                class: 'ck-heading_heading4'
                            },
                            {
                                model: 'heading5',
                                view: 'h5',
                                title: 'Heading 5',
                                class: 'ck-heading_heading5'
                            },
                            {
                                model: 'heading6',
                                view: 'h6',
                                title: 'Heading 6',
                                class: 'ck-heading_heading6'
                            }
                        ]
                    },
                    htmlSupport: {
                        allow: [
                            {
                                name: /^.*$/,
                                styles: true,
                                attributes: true,
                                classes: true
                            }
                        ]
                    },
                    licenseKey: LICENSE_KEY,
                    link: {
                        addTargetToExternalLinks: true,
                        defaultProtocol: 'https://',
                        decorators: {
                            toggleDownloadable: {
                                mode: 'manual',
                                label: 'Downloadable',
                                attributes: {
                                    download: 'file'
                                }
                            }
                        }
                    },
                    mention: {
                        feeds: [
                            {
                                marker: '@',
                                feed: [
                                    /* See: https://ckeditor.com/docs/ckeditor5/latest/features/mentions.html */
                                ]
                            }
                        ]
                    },
                    menuBar: {
                        isVisible: true
                    },
                    placeholder: 'Type or paste your content here!',
                    style: {
                        definitions: [
                            {
                                name: 'Article category',
                                element: 'h3',
                                classes: ['category']
                            },
                            {
                                name: 'Title',
                                element: 'h2',
                                classes: ['document-title']
                            },
                            {
                                name: 'Subtitle',
                                element: 'h3',
                                classes: ['document-subtitle']
                            },
                            {
                                name: 'Info box',
                                element: 'p',
                                classes: ['info-box']
                            },
                            {
                                name: 'Side quote',
                                element: 'blockquote',
                                classes: ['side-quote']
                            },
                            {
                                name: 'Marker',
                                element: 'span',
                                classes: ['marker']
                            },
                            {
                                name: 'Spoiler',
                                element: 'span',
                                classes: ['spoiler']
                            },
                            {
                                name: 'Code (dark)',
                                element: 'pre',
                                classes: ['fancy-code', 'fancy-code-dark']
                            },
                            {
                                name: 'Code (bright)',
                                element: 'pre',
                                classes: ['fancy-code', 'fancy-code-bright']
                            }
                        ]
                    },
                    table: {
                        contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties']
                    }
                };

                ClassicEditor.create(document.querySelector('#ckeditor'), editorConfig);

            }
        });


    </script>

</body>


<!-- Mirrored from smarthr.dreamstechnologies.com/laravel/template/public/admin-dashboard by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 12 Nov 2024 05:36:08 GMT -->

</html>