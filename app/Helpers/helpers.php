<?php 
    if (!function_exists('site_setting')) {
        function site_setting($key, $default = null)
        {
            return optional(App\Models\SiteSetting::where('key', $key)->first())->value ?? $default;
        }
    }

    if (!function_exists('formatPrice')) {
    function formatPrice($price) {
            if ($price < 100000) {
                // If the value is less than 1 Lakh, just format the number with ₹
                return "₹ " . number_format($price, 2);
            } elseif ($price < 10000000) {
                // For values in Lakh
                $priceInLakh = $price / 100000;
                return "₹ " . number_format($priceInLakh, 2) . " Lakh";
            } else {
                // For values in Crore
                $priceInCr = $price / 10000000;
                return "₹ " . number_format($priceInCr, 2) . " Cr";
            }
        }
    }

    if (!function_exists('formatPriceRange')) {
        function formatPriceRange($price) {
            if ($price < 100000) {
                // If the value is less than 1 Lakh, just format the number with ₹
                return number_format($price, 2);
            } elseif ($price < 10000000) {
                // For values in Lakh
                $priceInLakh = $price / 100000;
                return number_format($priceInLakh, 2) . ' Lakh';
            } else {
                // For values in Crore
                $priceInCr = $price / 10000000;
                return number_format($priceInCr, 2) . ' Crore';
            }
        }
    }

    // if (!function_exists('priceRange')) {
    //     function priceRange($prices) {
    //         dd($prices);
    //         // Sample price array (values in lakhs)
    //         // $prices = [13.85, 15.2, 18.9, 24.54];
            
    //         // Sort the array to ensure low to high order
    //         sort($prices);
            
    //         // Get the minimum and maximum values
    //         $minPrice = $prices[0];
    //         $maxPrice = $prices[count($prices) - 1];
            
    //         // Format the output
    //         $formattedPrice = "₹ " . formatPriceRange($minPrice) . " - " . formatPriceRange($maxPrice) . " Lakh";
            
    //         // Display the result
    //         return $formattedPrice;
            
    //     }
    // }

    if (!function_exists('priceRange')) {
        function priceRange($data) {
            $prices = [];
            // Loop through the dynamic array to find the first valid `price` array
            foreach ($data as $item) {
                if (isset($item['show_room_price']) && is_array($item['show_room_price']) && !empty($item['show_room_price'])) {
                    $prices[] = $item['show_room_price'][0]['price'];
                }else{
                    $prices[] = $item['price'];
                }
            }
    
            //Sort the array to ensure low to high order
            sort($prices);
    
            // Get the minimum and maximum values
            $minPrice = $prices[0];
            $maxPrice = $prices[count($prices) - 1];
    
            // Format the output
            $formattedPrice = "₹ " . formatPriceRange($minPrice) . " - " . formatPriceRange($maxPrice);
    
            return $formattedPrice;
        }
    }

    if (!function_exists('callPriceRange')) {
        function callPriceRange($vehicle) {
            $priceRange = $vehicle->variants->isNotEmpty() ? priceRange($vehicle->variants->toArray()) : formatPrice($vehicle->price);
    
            return $priceRange;
        }
    }

    if (!function_exists('numberFormat')) {
        function numberFormat($price) {
            $formattedPrice = "₹ " .number_format($price);
            return $formattedPrice;
        }
    }
?>