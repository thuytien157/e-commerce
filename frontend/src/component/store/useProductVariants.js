import { ref, watch, computed } from "vue";

export default function useProductVariants(product) {
  const selectedColor = ref(null);
  const selectedSize = ref(null);

  const uniqueColors = computed(() => {
    const colorSet = new Set();
    const colors = [];
    product.value?.variants.forEach((variant) => {
      const colorAttribute = variant.attributes.find(
        (attr) => attr.attribute_id === 2
      );
      if (colorAttribute && !colorSet.has(colorAttribute.attribute_value_id)) {
        colorSet.add(colorAttribute.attribute_value_id);
        colors.push(colorAttribute);
      }
    });
    return colors;
  });

  const uniqueSizes = computed(() => {
    const sizeSet = new Set();
    const sizes = [];
    product.value?.variants.forEach((variant) => {
      const sizeAttribute = variant.attributes.find(
        (attr) => attr.attribute_id === 1
      );
      if (sizeAttribute && !sizeSet.has(sizeAttribute.attribute_value_id)) {
        sizeSet.add(sizeAttribute.attribute_value_id);
        sizes.push(sizeAttribute);
      }
    });
    return sizes;
  });

  const currentVariant = computed(() => {
    if (!product.value) return null;
    return product.value.variants.find((variant) => {
      const hasSelectedColor = variant.attributes.some(
        (attr) =>
          attr.attribute_id === 2 &&
          attr.attribute_value_id === selectedColor.value
      );
      const hasSelectedSize = variant.attributes.some(
        (attr) =>
          attr.attribute_id === 1 &&
          attr.attribute_value_id === selectedSize.value
      );
      return hasSelectedColor && hasSelectedSize;
    });
  });

  const currentVariantImages = computed(() => {
    return (
      currentVariant.value?.images || product.value?.variants[0]?.images || []
    );
  });

  const mainImage = ref(null);

  const setupDefaultOptions = () => {
    if (product.value?.variants.length > 0) {
      const defaultVariant = product.value.variants[0];
      const defaultColor = defaultVariant.attributes.find(
        (attr) => attr.attribute_id === 2
      );
      const defaultSize = defaultVariant.attributes.find(
        (attr) => attr.attribute_id === 1
      );

      selectedColor.value = defaultColor
        ? defaultColor.attribute_value_id
        : null;
      selectedSize.value = defaultSize ? defaultSize.attribute_value_id : null;
    }
  };

  const isSizeAvailable = (sizeId) => {
    return product.value.variants.some((variant) => {
      const hasSelectedColor = variant.attributes.some(
        (attr) =>
          attr.attribute_id === 2 &&
          attr.attribute_value_id === selectedColor.value
      );
      const hasMatchingSize = variant.attributes.some(
        (attr) => attr.attribute_id === 1 && attr.attribute_value_id === sizeId
      );
      return hasSelectedColor && hasMatchingSize;
    });
  };

  watch(
    () => product.value,
    (newProduct) => {
      if (newProduct) {
        setupDefaultOptions();
      }
    },
    { immediate: true }
  );

  watch(
    currentVariant,
    (newVariant) => {
      if (newVariant && newVariant.images.length > 0) {
        mainImage.value = newVariant.image;
      } else if (
        product.value?.variants.length > 0 &&
        product.value.variants[0].images.length > 0
      ) {
        mainImage.value = product.value.variants[0].images[0].image_url;
      }
    },
    { immediate: true }
  );

  return {
    mainImage,
    selectedColor,
    selectedSize,
    uniqueColors,
    uniqueSizes,
    currentVariant,
    currentVariantImages,
    setupDefaultOptions,
    isSizeAvailable,
  };
}
