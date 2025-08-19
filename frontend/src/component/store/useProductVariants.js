import { ref, watch, computed } from "vue";

export default function useProductVariants(product) {
  const selectedAttributes = ref({});
  const mainImage = ref(null);

  const uniqueAttributes = computed(() => {
    const attributeMap = {};
    product.value?.variants.forEach((variant) => {
      variant.attributes.forEach((attribute) => {
        const attributeId = attribute.attribute_id;
        if (!attributeMap[attributeId]) {
          attributeMap[attributeId] = {
            id: attribute.attribute_id,
            name: attribute.attribute_name_display,
            values: [],
          };
        }
        const existingValue = attributeMap[attributeId].values.find(
          (val) => val.attribute_value_id === attribute.attribute_value_id
        );
        if (!existingValue) {
          attributeMap[attributeId].values.push(attribute);
        }
      });
    });
    return Object.values(attributeMap);
  });

  const currentVariant = computed(() => {
    if (!product.value) return null;
    return product.value.variants.find((variant) => {
      return Object.keys(selectedAttributes.value).every((attributeId) => {
        const selectedValueId = selectedAttributes.value[attributeId];
        return variant.attributes.some(
          (attr) =>
            attr.attribute_id.toString() === attributeId &&
            attr.attribute_value_id === selectedValueId
        );
      });
    });
  });

  const currentVariantImages = computed(() => {
    if (currentVariant.value?.images.length > 0) {
      return currentVariant.value.images;
    }
    return product.value?.variants[0]?.images || [];
  });

  const isAttributeAvailable = (attributeId, valueId) => {
    const otherSelectedAttributes = { ...selectedAttributes.value };
    delete otherSelectedAttributes[attributeId];

    return product.value.variants.some((variant) => {
      const hasMatchingAttribute = variant.attributes.some(
        (attr) =>
          attr.attribute_id === attributeId &&
          attr.attribute_value_id === valueId
      );
      if (!hasMatchingAttribute) return false;

      return Object.keys(otherSelectedAttributes).every((otherAttributeId) => {
        const otherSelectedValueId = otherSelectedAttributes[otherAttributeId];
        return variant.attributes.some(
          (attr) =>
            attr.attribute_id.toString() === otherAttributeId &&
            attr.attribute_value_id === otherSelectedValueId
        );
      });
    });
  };

  const setupDefaultOptions = () => {
    if (product.value?.variants.length > 0) {
      const defaultVariant = product.value.variants[0];
      const newSelectedAttributes = {};
      defaultVariant.attributes.forEach((attr) => {
        newSelectedAttributes[attr.attribute_id] = attr.attribute_value_id;
      });
      selectedAttributes.value = newSelectedAttributes;
    }
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
      if (newVariant && newVariant.image) {
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

  const selectAttribute = (attributeId, valueId) => {
    selectedAttributes.value[attributeId] = valueId;
    const newVariant = currentVariant.value;
    if (newVariant && newVariant.images.length > 0) {
      mainImage.value = newVariant.images[0].image_url;
    } else if (product.value?.variants[0]?.images.length > 0) {
      mainImage.value = product.value.variants[0].images[0].image_url;
    }
  };

  return {
    mainImage,
    selectedAttributes,
    uniqueAttributes,
    currentVariant,
    currentVariantImages,
    isAttributeAvailable,
    selectAttribute,
  };
}
