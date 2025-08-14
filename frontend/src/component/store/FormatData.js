import numeral from "numeral";

const FormatData = {
  formatNumber(value) {
    return numeral(value).format("0,0");
  },

  formatDate(dateStr) {
    if (!dateStr) return "";
    const date = new Date(dateStr);
    return date.toLocaleDateString("vi-VN"); // chỉ ngày
  },

  formatTime(dateStr) {
    if (!dateStr) return "";
    const date = new Date(dateStr);
    return date.toLocaleTimeString("vi-VN", {
      hour: "2-digit",
      minute: "2-digit",
    });
  },

  formatDateTime(dateStr) {
    if (!dateStr) return "";
    const d = new Date(dateStr);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, "0");
    const day = String(d.getDate()).padStart(2, "0");
    const hours = String(d.getHours()).padStart(2, "0");
    const minutes = String(d.getMinutes()).padStart(2, "0");
    return `${day}/${month}/${year} - ${hours}:${minutes}`;
  },

  uniqueColors(variants) {
    if (!variants || variants.length === 0) {
      return [];
    }

    const colorSet = new Set();
    const colors = [];

    variants.forEach((variant) => {
      const colorAttribute = variant.attributes.find(
        (attr) => attr.attribute_id === 2
      );
      if (colorAttribute && !colorSet.has(colorAttribute.attribute_value_id)) {
        colorSet.add(colorAttribute.attribute_value_id);
        colors.push(colorAttribute);
      }
    });

    return colors;
  },
  uniqueSizes(variants) {
    if (!variants || variants.length === 0) {
      return [];
    }

    const colorSet = new Set();
    const colors = [];

    variants.forEach((variant) => {
      const colorAttribute = variant.attributes.find(
        (attr) => attr.attribute_id === 1
      );
      if (colorAttribute && !colorSet.has(colorAttribute.attribute_value_id)) {
        colorSet.add(colorAttribute.attribute_value_id);
        colors.push(colorAttribute);
      }
    });

    return colors;
  },
};

export default FormatData;
